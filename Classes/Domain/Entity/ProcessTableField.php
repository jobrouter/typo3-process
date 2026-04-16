<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Domain\Entity;

use JobRouter\AddOn\Typo3Base\Enumeration\FieldType;

/**
 * The entity represents a row from the tx_jobrouterprocess_domain_model_processtablefield database table
 */
final readonly class ProcessTableField
{
    private function __construct(
        public int $uid,
        public string $name,
        public string $description,
        public FieldType $type,
        public int $fieldSize,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['uid'],
            $data['name'],
            $data['description'],
            FieldType::from((int) $data['type']),
            (int) $data['field_size'],
        );
    }
}
