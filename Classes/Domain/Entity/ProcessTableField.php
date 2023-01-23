<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Entity;

use Brotkrueml\JobRouterBase\Enumeration\FieldType;

final class ProcessTableField
{
    private function __construct(
        public readonly int $uid,
        public readonly string $name,
        public readonly string $description,
        public readonly FieldType $type,
        public readonly int $fieldSize,
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
            $data['description'],
            FieldType::from((int)$data['type']),
            (int)$data['field_size'],
        );
    }
}