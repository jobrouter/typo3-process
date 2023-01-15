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
use Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterProcess\Domain\Dto\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Model\Process;
use Brotkrueml\JobRouterProcess\Domain\Model\Processtablefield;
use Brotkrueml\JobRouterProcess\Domain\Model\Step;
use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
use Brotkrueml\JobRouterProcess\Exception\CommonParameterNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\InvalidFieldTypeException;
use Brotkrueml\JobRouterProcess\Exception\MissingFinisherOptionException;
use Brotkrueml\JobRouterProcess\Exception\MissingProcessTableFieldException;
use Brotkrueml\JobRouterProcess\Exception\ProcessNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\StepNotFoundException;
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
        private readonly StepRepository $stepRepository,
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

        $this->determineStep($handle);

        $this->initialiseTransfer();
        $this->prepareStepParametersForTransfer();
        $this->prepareTypeForTransfer();
        $this->prepareProcessTableForTransfer();
        $this->preparer->store($this->transfer);
    }

    private function determineStep(string $handle): void
    {
        /** @var Step|null $step */
        $step = $this->stepRepository->findOneByHandle($handle);
        if (! $step instanceof Step) {
            throw new StepNotFoundException(
                \sprintf(
                    'Step with handle "%s" is not available, defined in form with identifier "%s"',
                    $handle,
                    $this->getFormIdentifier(),
                ),
                1581270832,
            );
        }

        if (! $step->getProcess() instanceof Process) {
            throw new ProcessNotFoundException(
                \sprintf(
                    'Process for step with handle "%s" is not available, defined in form with identifier "%s"',
                    $handle,
                    $this->getFormIdentifier(),
                ),
                1581281395,
            );
        }

        $this->step = $step;
    }

    private function initialiseTransfer(): void
    {
        $this->transfer = new Transfer(
            \time(),
            (int)$this->step->getUid(),
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

            $value = $this->variableResolver->resolve(FieldTypeEnumeration::TEXT, $value);

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

        $this->transfer->setType((string)$this->variableResolver->resolve(FieldTypeEnumeration::TEXT, $type));
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
                        // @phpstan-ignore-next-line Already checked before if process is available
                        $this->step->getProcess()->getName(),
                    ),
                    1585930166,
                );
            }

            $value = $this->variableResolver->resolve(
                $processTableFields[$processTableField]->getType(),
                $value,
            );

            $value = $this->resolveFormFields($formValues, (string)$value);

            $processTable[$processTableField] = $this->considerTypeForFieldValue(
                $value,
                $processTableFields[$processTableField]->getType(),
                $processTableFields[$processTableField]->getFieldSize(),
            );
        }

        $this->transfer->setProcesstable(\json_encode($processTable, \JSON_THROW_ON_ERROR));
    }

    /**
     * @return Processtablefield[]
     */
    private function prepareProcessTableFields(): array
    {
        /** @var Processtablefield[] $fields */
        $fields = $this->step->getProcess()->getProcesstablefields(); // @phpstan-ignore-line Already checked before if process is available

        $processTableFields = [];
        foreach ($fields as $field) {
            $processTableFields[$field->getName()] = $field;
        }

        // @phpstan-ignore-next-line
        return $processTableFields;
    }

    private function considerTypeForFieldValue(string|int $value, int $type, int $fieldSize): string|int
    {
        if ($type === FieldTypeEnumeration::TEXT) {
            $value = (string)$value;

            if ($fieldSize !== 0) {
                return \mb_substr($value, 0, $fieldSize);
            }

            return $value;
        }

        if ($type === FieldTypeEnumeration::INTEGER) {
            return $value === '' ? '' : (int)$value;
        }

        if ($type === FieldTypeEnumeration::ATTACHMENT) {
            return $value;
        }

        throw new InvalidFieldTypeException(
            \sprintf('The field type "%d" is invalid', $type),
            1581344823,
        );
    }
}
