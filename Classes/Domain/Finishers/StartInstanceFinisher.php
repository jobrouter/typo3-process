<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Domain\Finishers;

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterProcess\Domain\Model\Processtablefield;
use Brotkrueml\JobRouterProcess\Domain\Model\Step;
use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
use Brotkrueml\JobRouterProcess\Enumeration\ProcessTableFieldTypeEnumeration;
use Brotkrueml\JobRouterProcess\Exception\CommonParameterNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\InvalidFieldTypeException;
use Brotkrueml\JobRouterProcess\Exception\MissingFinisherOptionException;
use Brotkrueml\JobRouterProcess\Exception\ProcessNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\StepNotFoundException;
use Brotkrueml\JobRouterProcess\Transfer\Preparer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;

final class StartInstanceFinisher extends AbstractFinisher implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var StepRepository */
    private $stepRepository;

    /** @var Preparer */
    private $preparer;

    private $commonParameters = [
        'initiator',
        'jobfunction',
        'pool',
        'priority',
        'simulation',
        'summary',
        'username',
    ];

    /** @var Step */
    private $step;

    /** @var Transfer */
    private $transfer;

    /**
     * Because there can be more than one instance to start, a form request id is generated
     * and used in addition to the form identifier to identify associated instances.
     * @var string
     */
    private $formRequestId = '';

    public function injectStepRepository(StepRepository $stepRepository): void
    {
        $this->stepRepository = $stepRepository;
    }

    protected function executeInternal()
    {
        $this->preparer = GeneralUtility::makeInstance(Preparer::class);
        $this->formRequestId = \substr(md5(uniqid('', true)), 0, 13);

        if (isset($this->options['handle'])) {
            $options = [$this->options];
        } else {
            $options = $this->options;
        }

        foreach ($options as $option) {
            $this->options = $option;
            $this->process();
        }
    }

    private function process(): void
    {
        $this->determineStep($this->parseOption('handle'));
        $this->defaultOptions = $this->step->getDefaultParameters();

        $this->initialiseTransfer();
        $this->prepareCommonParametersForTransfer();
        $this->prepareProcessTableForTransfer();
        $this->preparer->store($this->transfer);
    }

    private function determineStep(?string $handle): void
    {
        if (empty($handle)) {
            $message = \sprintf(
                'Step handle in StartInstanceFinisher of form with identifier "%s" is not defined.',
                $this->getFormIdentifier()
            );

            $this->logger->critical($message);

            throw new MissingFinisherOptionException($message, 1581270462);
        }

        $this->step = $this->stepRepository->findOneByHandle($handle);

        if (empty($this->step)) {
            $message = \sprintf(
                'Step with handle "%s" is not available, defined in form with identifier "%s"',
                $handle,
                $this->getFormIdentifier()
            );

            $this->logger->critical($message);

            throw new StepNotFoundException($message, 1581270832);
        }

        if (empty($this->step->getProcess())) {
            $message = \sprintf(
                'Process for step with handle "%s" is not available, defined in form with identifier "%s"',
                $handle,
                $this->getFormIdentifier()
            );

            $this->logger->critical($message);

            throw new ProcessNotFoundException($message, 1581281395);
        }
    }

    private function initialiseTransfer(): void
    {
        $this->transfer = new Transfer();
        $this->transfer->setStepUid($this->step->getUid());
        $this->transfer->setIdentifier($this->getFormIdentifier() . '_' . $this->formRequestId);
    }

    private function getFormIdentifier(): string
    {
        return $this
            ->finisherContext
            ->getFormRuntime()
            ->getFormDefinition()
            ->getIdentifier();
    }

    private function prepareCommonParametersForTransfer(): void
    {
        foreach ($this->commonParameters as $parameter) {
            $value = $this->parseOption($parameter);

            if (empty($value)) {
                continue;
            }

            $setter = 'set' . \ucfirst($parameter);
            if (!\method_exists($this->transfer, $setter)) {
                throw new CommonParameterNotFoundException(
                    \sprintf('Method "%s" in Transfer domain model not found', $setter),
                    1581703904
                );
            }

            $this->transfer->{'set' . \ucfirst($parameter)}($value);
        }
    }

    private function prepareProcessTableForTransfer(): void
    {
        if (!isset($this->options['processtable']) || !\is_array($this->options['processtable'])) {
            return;
        }

        $formValues = $this->finisherContext->getFormValues();
        $processTableFields = $this->prepareProcessTableFields();

        $processTable = [];

        foreach ($this->options['processtable'] as $processTableField => $configuration) {
            if (!\array_key_exists($processTableField, $processTableFields)) {
                $this->logger->warning(
                    \sprintf(
                        'Process table field "%s" is used in form with identifier "%s" but not defined in process "%s"',
                        $processTableField,
                        $this->getFormIdentifier(),
                        $this->step->getProcess()->getName()
                    )
                );
                continue;
            }

            if (isset($configuration['mapOnFormField']) && isset($formValues[$configuration['mapOnFormField']])) {
                $processTable[$processTableField] = $this->considerTypeForFieldValue(
                    $formValues[$configuration['mapOnFormField']],
                    $processTableFields[$processTableField]->getType()
                );
                continue;
            }

            if (isset($configuration['staticValue'])) {
                $processTable[$processTableField] = $this->considerTypeForFieldValue(
                    $configuration['staticValue'],
                    $processTableFields[$processTableField]->getType()
                );
                continue;
            }

            throw new MissingFinisherOptionException(
                \sprintf(
                    'The process table field "%s" has to be configured with either "mapOnFormField" or "staticValue"',
                    $processTableField
                ),
                1581345018
            );
        }

        $this->transfer->setProcesstable(\json_encode($processTable));
    }

    /**
     * @return Processtablefield[]
     */
    private function prepareProcessTableFields(): array
    {
        /** @var Processtablefield[] $fields */
        $fields = $this->step->getProcess()->getProcesstablefields();

        $processTableFields = [];
        foreach ($fields as $field) {
            $processTableFields[$field->getName()] = $field;
        }

        return $processTableFields;
    }

    private function considerTypeForFieldValue($value, int $type)
    {
        switch ($type) {
            case ProcessTableFieldTypeEnumeration::TEXT:
                return $value;
            case ProcessTableFieldTypeEnumeration::INTEGER:
                return (int)$value;
        }

        throw new InvalidFieldTypeException(
            \sprintf('The field type "%d" is invalid', $type),
            1581344823
        );
    }
}
