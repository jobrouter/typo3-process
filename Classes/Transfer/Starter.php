<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Transfer;

use Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterClient\Client\IncidentsClientDecorator;
use Brotkrueml\JobRouterClient\Model\Incident;
use Brotkrueml\JobRouterClient\Resource\File;
use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterProcess\Crypt\Transfer\Decrypter;
use Brotkrueml\JobRouterProcess\Domain\Entity\CountResult;
use Brotkrueml\JobRouterProcess\Domain\Model\Process;
use Brotkrueml\JobRouterProcess\Domain\Model\Processtablefield;
use Brotkrueml\JobRouterProcess\Domain\Model\Step;
use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Exception\ConnectionNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\FileNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\ProcessNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\ProcessTableFieldNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\StepNotFoundException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * @internal Only to be used within the jobrouter_process extension, not part of the public API
 */
class Starter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const INCIDENTS_RESOURCE_TEMPLATE = 'application/incidents/%s';

    private PersistenceManagerInterface $persistenceManager;
    private RestClientFactory $restClientFactory;
    private StepRepository $stepRepository;
    private Decrypter $decrypter;
    private TransferRepository $transferRepository;
    private ResourceFactory $resourceFactory;
    private int $totalTransfers = 0;
    private int $erroneousTransfers = 0;

    public function __construct(
        PersistenceManagerInterface $persistenceManager,
        RestClientFactory $restClientFactory,
        StepRepository $stepRepository,
        Decrypter $decrypter,
        TransferRepository $transferRepository,
        ResourceFactory $resourceFactory
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->restClientFactory = $restClientFactory;
        $this->stepRepository = $stepRepository;
        $this->decrypter = $decrypter;
        $this->transferRepository = $transferRepository;
        $this->resourceFactory = $resourceFactory;
    }

    public function run(): CountResult
    {
        $this->logger->info('Start instances');
        $transfers = $this->transferRepository->findByStartSuccess(0);

        $this->totalTransfers = 0;
        $this->erroneousTransfers = 0;
        foreach ($transfers as $transfer) {
            $this->processTransfer($transfer);
        }

        $this->logger->info(
            \sprintf(
                'Started %d instance(s) with %d errors',
                $this->totalTransfers,
                $this->erroneousTransfers
            )
        );

        return new CountResult($this->totalTransfers, $this->erroneousTransfers);
    }

    private function processTransfer(Transfer $transfer): void
    {
        $this->logger->debug(\sprintf('Processing transfer with uid "%d"', $transfer->getUid()));

        $this->totalTransfers++;
        try {
            $this->startTransfer($transfer);
        } catch (\Exception $e) {
            $this->erroneousTransfers++;
            // @phpstan-ignore-next-line
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
        $incident = $this->createIncidentFromTransferItem($step, $this->decrypter->decryptIfEncrypted($transfer));

        $client = $this->getRestClientForStep($step);
        $response = $client->request(
            'POST',
            \sprintf(self::INCIDENTS_RESOURCE_TEMPLATE, $step->getProcess()->getName()), // @phpstan-ignore-line
            $incident
        );

        $successMessage = '';
        $body = \json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);
        if (\is_array($body)) {
            $successMessage = $body['incidents'][0] ?? '';
        }

        $transfer->setStartSuccess(true);
        $transfer->setStartMessage(\is_array($successMessage) ? \json_encode($successMessage, JSON_THROW_ON_ERROR) : $successMessage);

        $this->logger->debug(
            \sprintf(
                'Response of starting the transfer with uid "%d": "%s"',
                $transfer->getUid(),
                $transfer->getStartMessage()
            )
        );
    }

    private function getStep(int $stepUid): Step
    {
        $step = $this->stepRepository->findByIdentifier($stepUid);

        if (! $step instanceof Step) {
            throw new StepNotFoundException(
                \sprintf(
                    'Step link with uid "%d" is not available',
                    $stepUid
                ),
                1581331820
            );
        }

        if (! $step->getProcess() instanceof Process) {
            throw new ProcessNotFoundException(
                \sprintf(
                    'Process for step link with handle "%s" is not available',
                    $step->getHandle()
                ),
                1635596424
            );
        }

        return $step;
    }

    private function getRestClientForStep(Step $step): IncidentsClientDecorator
    {
        static $clients = [];

        $process = $step->getProcess();
        if (! $process instanceof Process) {
            throw new ProcessNotFoundException(
                \sprintf(
                    'Process for step link with handle "%s" is not available',
                    $step->getHandle()
                ),
                1581331785
            );
        }

        $connection = $process->getConnection();
        if (! $connection instanceof \Brotkrueml\JobRouterConnector\Domain\Model\Connection) {
            throw new ConnectionNotFoundException(
                \sprintf(
                    'Connection for process link "%s" is not available',
                    $process->getName()
                ),
                1581331915
            );
        }

        $connectionUid = $connection->getUid();
        if ($clients[$connectionUid] ?? false) {
            return $clients[$connectionUid];
        }

        $client = $this->restClientFactory->create($connection);

        return $clients[$connectionUid] = new IncidentsClientDecorator($client);
    }

    private function createIncidentFromTransferItem(Step $step, Transfer $transfer): Incident
    {
        $incident = new Incident();
        $incident->setStep($step->getStepNumber()); // @phpstan-ignore-line
        if ($transfer->getInitiator() !== '') {
            $incident->setInitiator($transfer->getInitiator());
        }
        if ($transfer->getUsername() !== '') {
            $incident->setUsername($transfer->getUsername());
        }
        if ($transfer->getJobfunction() !== '') {
            $incident->setJobfunction($transfer->getJobfunction());
        }
        if ($transfer->getSummary() !== '') {
            $incident->setSummary($transfer->getSummary());
        }
        $incident->setPriority($transfer->getPriority()); // @phpstan-ignore-line
        $incident->setPool($transfer->getPool()); // @phpstan-ignore-line

        if ($transfer->getProcesstable() !== '') {
            try {
                $processTable = \json_decode($transfer->getProcesstable(), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $processTable = null;
            }

            foreach ($processTable ?? [] as $name => $value) {
                /** @var Process $process */
                $process = $step->getProcess();
                $configuredProcessTableField = $this->getProcessTableField($name, $process);

                if ($configuredProcessTableField->getType() === FieldTypeEnumeration::TEXT) {
                    // A numeric static value in form finisher can be an integer
                    $value = (string)$value;

                    if ($configuredProcessTableField->getFieldSize() !== 0) {
                        $value = \mb_substr($value, 0, $configuredProcessTableField->getFieldSize());
                    }
                }

                if ($configuredProcessTableField->getType() === FieldTypeEnumeration::ATTACHMENT && $value !== '') {
                    $file = $this->resourceFactory->getFileObjectFromCombinedIdentifier($value);
                    if (! $file instanceof FileInterface) {
                        throw new FileNotFoundException(
                            \sprintf('File with identifier "%s" is not available!', $value),
                            1664109447
                        );
                    }
                    $value = new File($file->getForLocalProcessing(false));
                }

                $incident->setProcessTableField($name, $value);
            }
        }

        return $incident;
    }

    private function getProcessTableField(string $name, Process $process): Processtablefield
    {
        $configuredProcessTableFields = $process->getProcesstablefields()->toArray();

        $processTableField = \array_filter(
            $configuredProcessTableFields,
            static fn ($field): bool =>
                /** @var Processtablefield $field */
                $name === $field->getName()
        );

        if ($processTableField === []) {
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
