<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Domain\Entity;

use JobRouter\AddOn\Typo3Process\Crypt\Transfer\EncryptedFieldsBitSet;

/**
 * The entity represents a row from the tx_jobrouterprocess_domain_model_transfer database table
 */
final readonly class Transfer
{
    private function __construct(
        public int $uid,
        public int $crdate,
        public int $stepUid,
        public string $correlationId,
        public string $initiator,
        public string $username,
        public string $jobfunction,
        public string $summary,
        public int $priority,
        public int $pool,
        public string $processtable,
        public EncryptedFieldsBitSet $encryptedFields,
        public bool $startSuccess,
        public ?\DateTimeImmutable $startDate,
        public string $startMessage,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $startDate = (int) $data['start_date'];

        return new self(
            (int) $data['uid'],
            (int) $data['crdate'],
            (int) $data['step_uid'],
            $data['correlation_id'],
            $data['initiator'],
            $data['username'],
            $data['jobfunction'],
            (string) $data['summary'],
            (int) $data['priority'],
            (int) $data['pool'],
            (string) $data['processtable'],
            new EncryptedFieldsBitSet((int) $data['encrypted_fields']),
            (bool) $data['start_success'],
            $startDate > 0 ? (new \DateTimeImmutable())->setTimestamp($startDate) : null,
            (string) $data['start_message'],
        );
    }
}
