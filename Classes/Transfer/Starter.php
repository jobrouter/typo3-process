<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Transfer;

use Brotkrueml\JobRouterBase\Enumeration\FieldType;
use Brotkrueml\JobRouterClient\Client\IncidentsClientDecorator;
use Brotkrueml\JobRouterClient\Enumerations\Priority;
use Brotkrueml\JobRouterClient\Model\Incident;
use Brotkrueml\JobRouterClient\Resource\File;
use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterProcess\Crypt\Transfer\Decrypter;
use Brotkrueml\JobRouterProcess\Domain\Dto\CountResult;
use Brotkrueml\JobRouterProcess\Domain\Dto\Transfer as TransferDto;
use Brotkrueml\JobRouterProcess\Domain\Entity\Process;
use Brotkrueml\JobRouterProcess\Domain\Entity\ProcessTableField;
use Brotkrueml\JobRouterProcess\Domain\Entity\Step;
use Brotkrueml\JobRouterProcess\Domain\Entity\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Hydrator\ProcessRelationsHydrator;
use Brotkrueml\JobRouterProcess\Domain\Hydrator\StepProcessHydrator;
use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Exception\FileNotFoundException;
use Brotkrueml\JobRouterProcess\Exception\ProcessTableFieldNotFoundException;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;

/**
 * @internal Only to be used within the jobrouter_process extension, not part of the public API
 */
class Starter
{
    private const INCIDENTS_RESOURCE_TEMPLATE = 'application/incidents/%s';
    private int $totalTransfers = 0;
    private int $erroneousTransfers = 0;

    public function __construct(
        private readonly Decrypter $decrypter,
        private readonly LoggerInterface $logger,
        private readonly ProcessRelationsHydrator $processRelationsHydrator,
        private readonly StepProcessHydrator $stepProcessHydrator,
        private readonly ResourceFactory $resourceFactory,
        private readonly RestClientFactory $restClientFactory,
        private readonly StepRepository $stepRepository,
        private readonly TransferRepository $transferRepository,
    ) {
    }

    public function run(): CountResult
    {
        $this->logger->info('Start instances');
        $transfers = $this->transferRepository->findNotStarted();

        $this->totalTransfers = 0;
        $this->erroneousTransfers = 0;
        foreach ($transfers as $transfer) {
            $this->processTransfer($transfer);
        }

        $this->logger->info(
            \sprintf(
                'Started %d instance(s) with %d errors',
                $this->totalTransfers,
                $this->erroneousTransfers,
            ),
        );

        return new CountResult($this->totalTransfers, $this->erroneousTransfers);
    }

    private function processTransfer(Transfer $transfer): void
    {
        $this->logger->debug(\sprintf('Processing transfer with uid "%d"', $transfer->uid));

        $this->totalTransfers++;
        try {
            $message = $this->startTransfer($transfer);
            $isSuccess = true;
        } catch (\Exception $e) {
            $isSuccess = false;
            $message = $e->getMessage();

            $this->erroneousTransfers++;
            $context = [
                'transfer uid' => $transfer->uid,
                'exception class' => $e::class,
                'exception code' => $e->getCode(),
            ];
            $this->logger->error($message, $context);
        }

        $this->transferRepository->updateStartFields($transfer->uid, $isSuccess, \time(), $message);
    }

    private function startTransfer(Transfer $transfer): string
    {
        $step = $this->getStep($transfer->stepUid);
        $incident = $this->createIncidentFromTransferItem($step, $this->decrypter->decryptIfEncrypted(TransferDto::fromEntity($transfer)));

        $client = $this->getRestClientForStep($step);
        $response = $client->request(
            'POST',
            \sprintf(self::INCIDENTS_RESOURCE_TEMPLATE, $step->process->name), // @phpstan-ignore-line
            $incident,
        );

        $successMessage = '';
        $body = \json_decode($response->getBody()->getContents(), true, flags: \JSON_THROW_ON_ERROR);
        if (\is_array($body)) {
            $successMessage = $body['incidents'][0] ?? '';
        }
        $successMessage = \is_array($successMessage) ? \json_encode($successMessage, \JSON_THROW_ON_ERROR) : $successMessage;

        $this->logger->debug(
            \sprintf(
                'Response of starting the transfer with uid "%d": "%s"',
                $transfer->uid,
                $successMessage,
            ),
        );

        return $successMessage;
    }

    private function getStep(int $stepUid): Step
    {
        $step = $this->stepProcessHydrator->hydrate($this->stepRepository->findByUid($stepUid));

        /** @phpstan-assert Process $step->process */
        return $step->withProcess($this->processRelationsHydrator->hydrate($step->process));
    }

    private function getRestClientForStep(Step $step): IncidentsClientDecorator
    {
        static $clients = [];

        if ($clients[$step->process->connectionUid] ?? false) {
            return $clients[$step->process->connectionUid];
        }

        $client = $this->restClientFactory->create($step->process->connection);

        return $clients[$step->process->connectionUid] = new IncidentsClientDecorator($client);
    }

    private function createIncidentFromTransferItem(Step $step, TransferDto $transfer): Incident
    {
        $incident = new Incident($step->stepNumber);
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
        $incident->setPriority(Priority::from($transfer->getPriority()));
        $incident->setPool($transfer->getPool());

        if ($transfer->getProcesstable() !== '') {
            try {
                $processTable = \json_decode($transfer->getProcesstable(), true, flags: \JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $processTable = null;
            }

            foreach ($processTable ?? [] as $name => $value) {
                $configuredProcessTableField = $this->getProcessTableField($name, $step->process);

                if ($configuredProcessTableField->type === FieldType::Text) {
                    // A numeric static value in form finisher can be an integer
                    $value = (string)$value;

                    if ($configuredProcessTableField->fieldSize !== 0) {
                        $value = \mb_substr($value, 0, $configuredProcessTableField->fieldSize);
                    }
                }

                if ($configuredProcessTableField->type === FieldType::Attachment && $value !== '') {
                    $file = $this->resourceFactory->getFileObjectFromCombinedIdentifier($value);
                    if (! $file instanceof FileInterface) {
                        throw new FileNotFoundException(
                            \sprintf('File with identifier "%s" is not available!', $value),
                            1664109447,
                        );
                    }
                    $value = new File($file->getForLocalProcessing(false));
                }

                $incident->setProcessTableField($name, $value);
            }
        }

        return $incident;
    }

    private function getProcessTableField(string $name, Process $process): ProcessTableField
    {
        $processTableField = \array_filter(
            $process->processTableFields,
            static fn ($field): bool =>
                $name === $field->name,
        );

        if ($processTableField === []) {
            throw new ProcessTableFieldNotFoundException(
                \sprintf(
                    'Process table field "%s" is not configured in process link "%s"',
                    $name,
                    $process->name,
                ),
                1582053551,
            );
        }

        return \array_shift($processTableField);
    }
}
