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
 * The entity represents a row from the tx_jobrouterprocess_domain_model_step database table
 */
final class Step
{
    private function __construct(
        public readonly int $uid,
        public readonly string $handle,
        public readonly string $name,
        public readonly int $processUid,
        public readonly int $stepNumber,
        public readonly bool $disabled,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['uid'],
            $data['handle'],
            $data['name'],
            (int) $data['process'],
            (int) $data['step_number'],
            (bool) $data['disabled'],
        );
    }
}
