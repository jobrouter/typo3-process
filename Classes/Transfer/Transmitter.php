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
use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterProcess\Domain\Model\Step;
use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Exception\ConnectionNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\StepNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\ProcessNotFoundException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * @internal Only to be used within the jobrouter_process extension, not part of the public API
 */
class Transmitter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const INCIDENTS_RESOURCE_TEMPLATE = '/application/incidents/%s';

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
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->persistenceManager = $persistenceManager ?? $objectManager->get(PersistenceManagerInterface::class);
        $this->transferRepository = $transferRepository ?? $objectManager->get(TransferRepository::class);
        $this->stepRepository = $stepRepository ?? $objectManager->get(StepRepository::class);
    }

    public function run(): array
    {
        $this->logger->info('Start instances');
        $transfers = $this->transferRepository->findByTransmitSuccess(0);

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
            $this->transmitTransfer($transfer);
        } catch (\Exception $e) {
            $this->erroneousNumbersOfTransfers++;
            $context = [
                'transfer uid' => $transfer->getUid(),
                'exception class' => \get_class($e),
                'exception code' => $e->getCode(),
            ];
            $this->logger->error($e->getMessage(), $context);
            $transfer->setTransmitMessage($e->getMessage());
        }

        $transfer->setTransmitDate(new \DateTime());
        $this->transferRepository->update($transfer);
        $this->persistenceManager->persistAll();
    }

    private function transmitTransfer(Transfer $transfer): void
    {
        $step = $this->getStep($transfer->getStepUid());
        $incident = $this->createIncidentFromTransferItem($step->getStepNumber(), $transfer);

        $client = $this->getRestClientForStep($step);
        $response = $client->request(
            'POST',
            \sprintf(self::INCIDENTS_RESOURCE_TEMPLATE, $step->getProcess()->getName()),
            $incident
        );

        $successMessage = $response->getBody()->getContents();

        $transfer->setTransmitSuccess(true);
        $transfer->setTransmitMessage($successMessage);

        $this->logger->info(
            \sprintf(
                'Response of transmission of transfer with uid "%d": "%s"',
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

    private function createIncidentFromTransferItem(int $step, Transfer $transfer): Incident
    {
        $transferData = \json_decode($transfer->getData(), true);

        $incident = new Incident();
        $incident->setStep($step);
        if (isset($transferData['initiator'])) {
            $incident->setInitiator($transferData['initiator']);
        }
        if (isset($transferData['username'])) {
            $incident->setUsername($transferData['username']);
        }
        if (isset($transferData['jobfunction'])) {
            $incident->setJobfunction($transferData['jobfunction']);
        }
        if (isset($transferData['summary'])) {
            $incident->setSummary($transferData['summary']);
        }
        if (isset($transferData['priority'])) {
            $incident->setPriority((int)$transferData['priority']);
        }
        if (isset($transferData['pool'])) {
            $incident->setPool((int)$transferData['pool']);
        }
        foreach ($transferData['processtable'] ?? [] as $name => $value) {
            $incident->setProcessTableField($name, $value);
        }

        return $incident;
    }
}
