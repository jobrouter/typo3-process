<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Domain\Finishers;

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterProcess\Domain\Model\Instance;
use Brotkrueml\JobRouterProcess\Domain\Model\Processtablefield;
use Brotkrueml\JobRouterProcess\Domain\Repository\InstanceRepository;
use Brotkrueml\JobRouterProcess\Exception\InstanceNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\MissingFinisherOptionException;
use Brotkrueml\JobRouterProcess\Exception\ProcessNotFoundException;
use Brotkrueml\JobRouterProcess\Transfer\Preparer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;

final class StartInstanceFinisher extends AbstractFinisher implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var InstanceRepository */
    private $instanceRepository;

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

    /** @var Instance */
    private $instance;

    private $data = [];

    /**
     * Because there can be more than one instance to start, a form request id is generated
     * and used in addition to the form identifier to identify associated instances.
     * @var string
     */
    private $formRequestId = '';

    public function injectInstanceRepository(InstanceRepository $instanceRepository): void
    {
        $this->instanceRepository = $instanceRepository;
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
        $this->determineInstance($this->parseOption('handle'));
        $this->defaultOptions = $this->instance->getDefaultParameters();

        $this->prepareData();
        $this->storeInTransferTable();
    }

    private function determineInstance(?string $handle): void
    {
        if (empty($handle)) {
            $message = \sprintf(
                'Instance handle in StartInstanceFinisher of form with identifier "%s" is not defined.',
                $this->getFormIdentifier()
            );

            $this->logger->critical($message);

            throw new MissingFinisherOptionException($message, 1581270462);
        }

        /** @var Instance $instance */
        $this->instance = $this->instanceRepository->findOneByHandle($handle);

        if (empty($this->instance)) {
            $message = \sprintf(
                'Instance with handle "%s" is not available, defined in form with identifier "%s"',
                $handle,
                $this->getFormIdentifier()
            );

            $this->logger->critical($message);

            throw new InstanceNotFoundException($message, 1581270832);
        }

        if (empty($this->instance->getProcess())) {
            $message = \sprintf(
                'Process for instance with handle "%s" is not available, defined in form with identifier "%s"',
                $handle,
                $this->getFormIdentifier()
            );

            $this->logger->critical($message);

            throw new ProcessNotFoundException($message, 1581281395);
        }
    }

    private function getFormIdentifier(): string
    {
        return $this
            ->finisherContext
            ->getFormRuntime()
            ->getFormDefinition()
            ->getIdentifier();
    }

    private function prepareData(): void
    {
        $this->data = [];
        foreach ($this->commonParameters as $parameter) {
            $value = $this->parseOption($parameter);

            if (empty($value)) {
                continue;
            }

            $this->data[$parameter] = $value;
        }

        if (!isset($this->options['processtable']) || !\is_array($this->options['processtable'])) {
            return;
        }

        $this->data['processtable'] = [];
        $formValues = $this->finisherContext->getFormValues();
        $processTableFields = $this->prepareProcessTableFields();

        foreach ($this->options['processtable'] as $processTableField => $configuration) {
            if (!\array_key_exists($processTableField, $processTableFields)) {
                $this->logger->warning(
                    \sprintf(
                        'Process table field "%s" is used in form with identifier "%s" but not defined in process "%s"',
                        $processTableField,
                        $this->getFormIdentifier(),
                        $this->instance->getProcess()->getName()
                    )
                );
                continue;
            }

            if (isset($configuration['mapOnFormField']) && isset($formValues[$configuration['mapOnFormField']])) {
                // @todo Check type
                $this->data['processtable'][$processTableField] = $configuration['mapOnFormField'];
            }
        }
    }

    private function prepareProcessTableFields(): array
    {
        /** @var Processtablefield[] $fields */
        $fields = $this->instance->getProcess()->getProcesstablefields();

        $processTableFields = [];
        foreach ($fields as $field) {
            $processTableFields[$field->getName()] = $field;
        }

        return $processTableFields;
    }

    private function storeInTransferTable(): void
    {
        $this->preparer->store(
            $this->instance->getUid(),
            $this->getFormIdentifier() . '_' . $this->formRequestId,
            \json_encode($this->data)
        );
    }
}
