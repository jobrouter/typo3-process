<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Transfer;

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterClient\Client\IncidentsClientDecorator;
use Brotkrueml\JobRouterClient\Model\Incident;
use Brotkrueml\JobRouterConnector\Domain\Model\Connection;
use Brotkrueml\JobRouterProcess\Domain\Model\Process;
use Brotkrueml\JobRouterProcess\Domain\Model\Processtablefield;
use Brotkrueml\JobRouterProcess\Domain\Model\Step;
use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterProcess\Exception\ConnectionNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\ProcessNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\ProcessTableFieldNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\StepNotFoundException;
use Brotkrueml\JobRouterProcess\RestClient\RestClientFactory;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * @internal Only to be used within the jobrouter_process extension, not part of the public API
 */
class Starter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const INCIDENTS_RESOURCE_TEMPLATE = 'application/incidents/%s';

    /** @var PersistenceManagerInterface */
    private $persistenceManager;

    /** @var TransferRepository */
    private $transferRepository;

    /** @var StepRepository */
    private $stepRepository;

    /** @var RestClientFactory */
    private $restClientFactory;

    private static $clients = [];

    private $totalNumbersOfTransfers = 0;
    private $erroneousNumbersOfTransfers = 0;

    public function __construct(
        PersistenceManagerInterface $persistenceManager = null,
        TransferRepository $transferRepository = null,
        StepRepository $stepRepository = null
    ) {
        if ($persistenceManager === null || $transferRepository === null || $stepRepository === null) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $this->persistenceManager = $objectManager->get(PersistenceManagerInterface::class);
            $this->transferRepository = $objectManager->get(TransferRepository::class);
            $this->stepRepository = $objectManager->get(StepRepository::class);
            return;
        }

        $this->persistenceManager = $persistenceManager;
        $this->transferRepository = $transferRepository;
        $this->stepRepository = $stepRepository;
    }

    public function run(): array
    {
        $this->logger->info('Start instances');
        $transfers = $this->transferRepository->findByStartSuccess(0);

        $this->totalNumbersOfTransfers = 0;
        $this->erroneousNumbersOfTransfers = 0;
        foreach ($transfers as $transfer) {
            $this->processTransfer($transfer);
        }

        $this->logger->info(
            \sprintf(
                'Started %d instance(s) with %d errors',
                $this->totalNumbersOfTransfers,
                $this->erroneousNumbersOfTransfers
            )
        );

        return [$this->totalNumbersOfTransfers, $this->erroneousNumbersOfTransfers];
    }

    private function processTransfer(Transfer $transfer): void
    {
        $this->logger->debug(\sprintf('Processing transfer with uid "%d"', $transfer->getUid()));

        $this->totalNumbersOfTransfers++;
        try {
            $this->startTransfer($transfer);
        } catch (\Exception $e) {
            $this->erroneousNumbersOfTransfers++;
            $context = [
                'transfer uid' => $transfer->getUid(),
                'exception class' => \get_class($e),
                'exception code' => $e->getCode(),
            ];
            $this->logger->error($e->getMessage(), $context);
            $transfer->setStartMessage($e->getMessage());
        }

        $transfer->setStartDate(new \DateTime());
        $this->transferRepository->update($transfer);
        $this->persistenceManager->persistAll();
    }

    private function startTransfer(Transfer $transfer): void
    {
        $step = $this->getStep($transfer->getStepUid());
        $incident = $this->createIncidentFromTransferItem($step, $transfer);

        $client = $this->getRestClientForStep($step);
        $response = $client->request(
            'POST',
            \sprintf(self::INCIDENTS_RESOURCE_TEMPLATE, $step->getProcess()->getName()),
            $incident
        );

        $successMessage = '';
        if ($body = \json_decode($response->getBody()->getContents(), true)) {
            $successMessage = $body['incidents'][0] ?? '';
        }

        $transfer->setStartSuccess(true);
        $transfer->setStartMessage(\is_array($successMessage) ? \json_encode($successMessage) : $successMessage);

        $this->logger->debug(
            \sprintf(
                'Response of starting the transfer with uid "%d": "%s"',
                $transfer->getUid(),
                $successMessage
            )
        );
    }

    private function getStep(int $stepUid): Step
    {
        /** @var Step $step */
        $step = $this->stepRepository->findByIdentifier($stepUid);

        if (empty($step)) {
            throw new StepNotFoundException(
                \sprintf(
                    'Step link with uid "%d" is not available',
                    $stepUid
                ),
                1581331820
            );
        }

        return $step;
    }

    private function getRestClientForStep(Step $step): IncidentsClientDecorator
    {
        $process = $step->getProcess();
        if (empty($process)) {
            throw new ProcessNotFoundException(
                \sprintf(
                    'Process for step link with handle "%s" is not available',
                    $step->getHandle()
                ),
                1581331785
            );
        }

        /** @var Connection $connection */
        $connection = $process->getConnection();
        if (empty($connection)) {
            throw new ConnectionNotFoundException(
                \sprintf(
                    'Connection for process link "%s" is not available',
                    $process->getName()
                ),
                1581331915
            );
        }

        $connectionUid = $connection->getUid();
        if (static::$clients[$connectionUid] ?? false) {
            return static::$clients[$connectionUid];
        }

        $client = $this->getRestClientFactory()->create($connection);

        return static::$clients[$connectionUid] = new IncidentsClientDecorator($client);
    }

    private function getRestClientFactory(): RestClientFactory
    {
        if ($this->restClientFactory) {
            return $this->restClientFactory;
        }

        return $this->restClientFactory = new RestClientFactory();
    }

    private function createIncidentFromTransferItem(Step $step, Transfer $transfer): Incident
    {
        $incident = new Incident();
        $incident->setStep($step->getStepNumber());
        if (!empty($transfer->getInitiator())) {
            $incident->setInitiator($transfer->getInitiator());
        }
        if (!empty($transfer->getUsername())) {
            $incident->setUsername($transfer->getUsername());
        }
        if (!empty($transfer->getJobfunction())) {
            $incident->setJobfunction($transfer->getJobfunction());
        }
        if (!empty($transfer->getSummary())) {
            $incident->setSummary($transfer->getSummary());
        }
        if (!empty($transfer->getPriority())) {
            $incident->setPriority($transfer->getPriority());
        }
        if (!empty($transfer->getPool())) {
            $incident->setPool($transfer->getPool());
        }

        if (!empty($transfer->getProcesstable())) {
            $processTable = \json_decode($transfer->getProcesstable(), true);

            foreach ($processTable ?? [] as $name => $value) {
                $configuredProcessTableField = $this->getProcessTableField($name, $step->getProcess());

                if ($configuredProcessTableField->getType() === FieldTypeEnumeration::TEXT) {
                    // A numeric static value in form finisher can be an integer
                    $value = (string)$value;

                    if ($configuredProcessTableField->getFieldSize()) {
                        $value = \substr($value, 0, $configuredProcessTableField->getFieldSize());
                    }
                }

                $incident->setProcessTableField($name, $value);
            }
        }

        return $incident;
    }

    private function getProcessTableField(string $name, Process $process): Processtablefield
    {
        $configuredProcessTableFields = $process->getProcesstablefields()->toArray();

        $processTableField = \array_filter($configuredProcessTableFields, function ($field) use ($name) {
            /** @var Processtablefield $field */
            return $name === $field->getName();
        });

        if (empty($processTableField)) {
            throw new ProcessTableFieldNotFoundException(
                \sprintf(
                    'Process table field "%s" is not configured in process link "%s"',
                    $name,
                    $process->getName()
                ),
                1582053551
            );
        }

        return \array_shift($processTableField);
    }
}
