<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Finishers;

use Brotkrueml\JobRouterBase\Domain\Finishers\AbstractTransferFinisher;
use Brotkrueml\JobRouterBase\Domain\Preparers\FormFieldValuesPreparer;
use Brotkrueml\JobRouterBase\Enumeration\FieldType;
use Brotkrueml\JobRouterProcess\Domain\Dto\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Entity\ProcessTableField;
use Brotkrueml\JobRouterProcess\Domain\Entity\Step;
use Brotkrueml\JobRouterProcess\Domain\Hydrator\StepProcessHydrator;
use Brotkrueml\JobRouterProcess\Domain\Repository\ProcessTableFieldRepository;
use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
use Brotkrueml\JobRouterProcess\Exception\CommonParameterNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\InvalidFieldTypeException;
use Brotkrueml\JobRouterProcess\Exception\MissingFinisherOptionException;
use Brotkrueml\JobRouterProcess\Exception\MissingProcessTableFieldException;
use Brotkrueml\JobRouterProcess\Transfer\Preparer;

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

    private ?Step $step = null;
    private Transfer $transfer;

    /**
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(
        private readonly Preparer $preparer,
        private readonly ProcessTableFieldRepository $processTableFieldRepository,
        private readonly StepRepository $stepRepository,
        private readonly StepProcessHydrator $stepProcessHydrator,
    ) {
    }

    protected function process(): void
    {
        $handle = $this->parseOption('handle');
        if (! \is_string($handle) || $handle === '') {
            throw new MissingFinisherOptionException(
                \sprintf(
                    'Step handle in StartInstanceFinisher of form with identifier "%s" is not defined.',
                    $this->getFormIdentifier(),
                ),
                1581270462,
            );
        }

        $this->step = $this->stepProcessHydrator->hydrate($this->stepRepository->findByHandle($handle));

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
                throw new CommonParameterNotFoundException(
                    \sprintf('Method "%s" in Transfer DTO not found', $setter),
                    1581703904,
                );
            }

            $value = $this->variableResolver->resolve(FieldType::Text, $value);

            if ($property === 'priority' || $property === 'pool') {
                $value = (int)$value;
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

        $this->transfer->setType((string)$this->variableResolver->resolve(FieldType::Text, $type));
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
                throw new MissingProcessTableFieldException(
                    \sprintf(
                        'Process table field "%s" is used in form with identifier "%s" but not defined in process link "%s"',
                        $processTableField,
                        $this->getFormIdentifier(),
                        $this->step->process->name,
                    ),
                    1585930166,
                );
            }

            $value = $this->variableResolver->resolve(
                $processTableFields[$processTableField]->type,
                $value,
            );

            $value = $this->resolveFormFields($formValues, (string)$value);

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

    private function considerTypeForFieldValue(string|int $value, FieldType $type, int $fieldSize): string|int
    {
        if ($type === FieldType::Text) {
            $value = (string)$value;

            if ($fieldSize !== 0) {
                return \mb_substr($value, 0, $fieldSize);
            }

            return $value;
        }

        if ($type === FieldType::Integer) {
            return $value === '' ? '' : (int)$value;
        }

        if ($type === FieldType::Attachment) {
            return $value;
        }

        throw new InvalidFieldTypeException(
            \sprintf('The field type "%s" is invalid', $type->name),
            1581344823,
        );
    }
}
