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
final class Transfer
{
    private function __construct(
        public readonly int $uid,
        public readonly int $crdate,
        public readonly int $stepUid,
        public readonly string $correlationId,
        public readonly string $type,
        public readonly string $initiator,
        public readonly string $username,
        public readonly string $jobfunction,
        public readonly string $summary,
        public readonly int $priority,
        public readonly int $pool,
        public readonly string $processtable,
        public readonly EncryptedFieldsBitSet $encryptedFields,
        public readonly bool $startSuccess,
        public readonly ?\DateTimeImmutable $startDate,
        public readonly string $startMessage,
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
            $data['type'],
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
