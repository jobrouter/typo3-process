<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Domain\Demand;

use JobRouter\AddOn\Typo3Connector\Domain\Entity\Connection;
use JobRouter\AddOn\Typo3Process\Domain\Entity\ProcessTableField;
use JobRouter\AddOn\Typo3Process\Domain\Entity\Step;

/**
 * @internal
 */
final readonly class ProcessDemand
{
    /**
     * @param ProcessTableField[] $processTableFields
     * @param Step[] $steps
     */
    public function __construct(
        public int $uid,
        public string $name,
        public ?Connection $connection,
        public array $processTableFields,
        public array $steps,
        public bool $disabled,
    ) {}
}
