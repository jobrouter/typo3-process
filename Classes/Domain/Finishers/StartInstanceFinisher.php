<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Domain\Finishers;

use JobRouter\AddOn\Typo3Base\Domain\Finishers\AbstractTransferFinisher;
use JobRouter\AddOn\Typo3Base\Domain\Preparers\FormFieldValuesPreparer;
use JobRouter\AddOn\Typo3Base\Enumeration\FieldType;
use JobRouter\AddOn\Typo3Process\Domain\Dto\Transfer;
use JobRouter\AddOn\Typo3Process\Domain\Entity\ProcessTableField;
use JobRouter\AddOn\Typo3Process\Domain\Entity\Step;
use JobRouter\AddOn\Typo3Process\Domain\Repository\ProcessTableFieldRepository;
use JobRouter\AddOn\Typo3Process\Domain\Repository\StepRepository;
use JobRouter\AddOn\Typo3Process\Exception\CommonParameterNotFoundException;
use JobRouter\AddOn\Typo3Process\Exception\InvalidFieldTypeException;
use JobRouter\AddOn\Typo3Process\Exception\MissingFinisherOptionException;
use JobRouter\AddOn\Typo3Process\Exception\MissingProcessTableFieldException;
use JobRouter\AddOn\Typo3Process\Transfer\Preparer;

/**
 * @internal
 */
final class StartInstanceFinisher extends AbstractTransferFinisher
{
    /**
     * @var string[]
     */
    private array $stepParameters = [
        'initiator',
        'jobfunction',
        'pool',
        'priority',
        'simulation',
        'summary',
        'username',
    ];

    private Step $step;
    private Transfer $transfer;

    public function __construct(
        private readonly Preparer $preparer,
        private readonly ProcessTableFieldRepository $processTableFieldRepository,
        private readonly StepRepository $stepRepository,
    ) {}

    protected function process(): void
    {
        $handle = $this->parseOption('handle');
        if (! \is_string($handle) || $handle === '') {
            throw MissingFinisherOptionException::forStepWithFormIdentifier($this->getFormIdentifier());
        }

        $this->step = $this->stepRepository->findByHandle($handle);

        $this->initialiseTransfer();
        $this->prepareStepParametersForTransfer();
        $this->prepareTypeForTransfer();
        $this->prepareProcessTableForTransfer();
        $this->preparer->store($this->transfer);
    }

    private function initialiseTransfer(): void
    {
        $this->transfer = new Transfer(
            \time(),
            $this->step->uid,
            $this->correlationId,
        );
    }

    private function prepareStepParametersForTransfer(): void
    {
        foreach ($this->stepParameters as $property) {
            $value = $this->parseOption($property);
            if (! \is_string($value)) {
                continue;
            }
            if ($value === '') {
                continue;
            }

            $setter = 'set' . \ucfirst($property);
            if (! \method_exists($this->transfer, $setter)) {
                throw CommonParameterNotFoundException::forMethod($setter);
            }

            $value = $this->variableResolver->resolve(FieldType::Text, $value);

            if ($property === 'priority' || $property === 'pool') {
                $value = (int) $value;
            }

            $this->transfer->{$setter}($value);
        }
    }

    private function prepareTypeForTransfer(): void
    {
        $type = $this->parseOption('type');
        if (! \is_string($type)) {
            return;
        }
        if ($type === '') {
            return;
        }

        $this->transfer->setType((string) $this->variableResolver->resolve(FieldType::Text, $type));
    }

    private function prepareProcessTableForTransfer(): void
    {
        if (! isset($this->options['processtable'])) {
            return;
        }
        if (! \is_array($this->options['processtable'])) {
            return;
        }
        $formValues = (new FormFieldValuesPreparer())->prepareForSubstitution(
            $this->finisherContext->getFormRuntime()->getFormDefinition()->getElements(),
            $this->finisherContext->getFormValues(),
        );
        $processTableFields = $this->prepareProcessTableFields();
        $processTable = [];
        foreach ($this->options['processtable'] as $processTableField => $value) {
            if (! \array_key_exists($processTableField, $processTableFields)) {
                throw MissingProcessTableFieldException::forField(
                    $processTableField,
                    $this->step->processUid,
                    $this->getFormIdentifier(),
                );
            }

            $value = $this->variableResolver->resolve(
                $processTableFields[$processTableField]->type,
                $value,
            );

            $value = $this->resolveFormFields($formValues, (string) $value);

            $processTable[$processTableField] = $this->considerTypeForFieldValue(
                $value,
                $processTableFields[$processTableField]->type,
                $processTableFields[$processTableField]->fieldSize,
            );
        }

        $this->transfer->setProcesstable(\json_encode($processTable, \JSON_THROW_ON_ERROR));
    }

    /**
     * @return ProcessTableField[]
     */
    private function prepareProcessTableFields(): array
    {
        $fields = $this->processTableFieldRepository->findByProcessUid($this->step->processUid);

        $processTableFields = [];
        foreach ($fields as $field) {
            $processTableFields[$field->name] = $field;
        }

        return $processTableFields;
    }

    private function considerTypeForFieldValue(string $value, FieldType $type, int $fieldSize): string|int
    {
        if ($type === FieldType::Text) {
            if ($fieldSize !== 0) {
                return \mb_substr($value, 0, $fieldSize);
            }

            return $value;
        }

        if ($type === FieldType::Integer) {
            return $value === '' ? '' : (int) $value;
        }

        if ($type === FieldType::Date) {
            return (new \DateTimeImmutable($value))->setTime(0, 0)->format('c');
        }

        if ($type === FieldType::Attachment) {
            return $value;
        }

        throw InvalidFieldTypeException::forFieldType($type);
    }
}
