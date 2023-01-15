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
use Brotkrueml\JobRouterConnector\Service\Crypt;
use Brotkrueml\JobRouterProcess\Crypt\Transfer\EncryptedFieldsBitSet;
use Brotkrueml\JobRouterProcess\Domain\Model\Process;
use Brotkrueml\JobRouterProcess\Domain\Repository\ProcessRepository;
use Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterProcess\Exception\DeleteException;
use Psr\Log\LoggerInterface;

/**
 * @internal Only to be used within the jobrouter_process extension, not part of the public API
 */
class Deleter
{
    /**
     * @var array<int, string[]> Key is the process uid, the values are the process table fields defined as attachment
     */
    private array $attachmentFieldsForProcess = [];

    public function __construct(
        private readonly AttachmentDeleter $attachmentDeleter,
        private readonly Crypt $crypt,
        private readonly LoggerInterface $logger,
        private readonly ProcessRepository $processRepository,
        private readonly TransferRepository $transferRepository,
    ) {
    }

    public function run(int $ageInDays): int
    {
        $this->logger->info('Starting clean up of old transfers');

        $maximumTimestampForDeletion = \time() - $ageInDays * 86400;

        $this->logger->debug('Maximum timestamp for deletion: ' . $maximumTimestampForDeletion);

        try {
            $oldTransfers = $this->transferRepository->findForDeletion($maximumTimestampForDeletion);
            $countSuccessful = 0;
            $countErroneous = 0;
            foreach ($oldTransfers as $transfer) {
                $this->deleteTransfer($transfer) ? $countSuccessful++ : $countErroneous++;
            }
        } catch (\Exception $e) {
            $message = 'Error on clean up of old transfers: ' . $e->getMessage();
            $this->logger->error($message);
            throw new DeleteException($message, 1582133383, $e);
        }

        if ($countSuccessful === 0 && $countErroneous === 0) {
            $this->logger->info('No transfers deleted');
            return 0;
        }

        $this->logger->notice(
            \sprintf(
                '%d deleted transfer(s) successfully, %d with errors',
                $countSuccessful,
                $countErroneous,
            ),
        );

        return $countSuccessful;
    }

    /**
     * @param array<string, int|string|null> $transfer
     */
    private function deleteTransfer(array $transfer): bool
    {
        if (! isset($this->attachmentFieldsForProcess[$transfer['process_uid']])) {
            $this->attachmentFieldsForProcess[$transfer['process_uid']] = $this->getAttachmentFieldsForProcess((int)$transfer['process_uid']);
        }

        if ($this->attachmentFieldsForProcess[$transfer['process_uid']] !== []) {
            $encryptedFields = new EncryptedFieldsBitSet((int)$transfer['encrypted_fields']);
            if ($encryptedFields->get(EncryptedFieldsBitSet::PROCESSTABLE)) {
                $transfer['processtable'] = $this->crypt->decrypt((string)$transfer['processtable']);
            }
            $processtable = \json_decode($transfer['processtable'], true, 512, \JSON_THROW_ON_ERROR);
            $this->deleteAttachments($processtable, $this->attachmentFieldsForProcess[$transfer['process_uid']]);
        }

        $deletedRows = $this->transferRepository->delete((int)$transfer['uid']);
        if ($deletedRows > 0) {
            $this->logger->info(\sprintf('Transfer with uid "%d" was deleted successfully.', $transfer['uid']));
            return true;
        }

        $this->logger->warning(\sprintf('Transfer with uid "%d" could not be deleted.', $transfer['uid']));
        return false;
    }

    /**
     * @return string[]
     */
    private function getAttachmentFieldsForProcess(int $processUid): array
    {
        /** @var Process|null $process */
        $process = $this->processRepository->findByIdentifier($processUid);

        if (! $process instanceof Process) {
            return [];
        }

        $attachmentFields = [];
        foreach ($process->getProcesstablefields() as $field) {
            if ($field->getType() === FieldTypeEnumeration::ATTACHMENT) {
                $attachmentFields[] = $field->getName();
            }
        }

        return $attachmentFields;
    }

    /**
     * @param array<string, mixed> $processtable
     * @param string[] $attachmentFields
     */
    private function deleteAttachments(array $processtable, array $attachmentFields): void
    {
        foreach ($attachmentFields as $field) {
            if ($processtable[$field] ?? false) {
                $this->attachmentDeleter->deleteFile($processtable[$field]);
            }
        }
    }
}
