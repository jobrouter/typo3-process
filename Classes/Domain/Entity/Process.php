<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Domain\Entity;

/**
 * The entity represents a row from the tx_jobrouterprocess_domain_model_process database table
 */
final readonly class Process
{
    private function __construct(
        public int $uid,
        public string $name,
        public int $connectionUid,
        public bool $disabled,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['uid'],
            $data['name'],
            (int) $data['connection'],
            (bool) $data['disabled'],
        );
    }
}
