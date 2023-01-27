<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Entity;

/**
 * The entity represents a row from the tx_jobrouterprocess_domain_model_process database table
 */
final class Process
{
    private function __construct(
        public readonly int $uid,
        public readonly string $name,
        public readonly int $connectionUid,
        public readonly bool $disabled,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int)$data['uid'],
            $data['name'],
            (int)$data['connection'],
            (bool)$data['disabled'],
        );
    }
}
