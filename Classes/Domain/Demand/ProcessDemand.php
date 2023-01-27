<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Demand;

use Brotkrueml\JobRouterConnector\Domain\Entity\Connection;
use Brotkrueml\JobRouterProcess\Domain\Entity\ProcessTableField;
use Brotkrueml\JobRouterProcess\Domain\Entity\Step;

/**
 * @internal
 */
final class ProcessDemand
{
    /**
     * @param ProcessTableField[] $processTableFields
     * @param Step[] $steps
     */
    public function __construct(
        public readonly int $uid,
        public readonly string $name,
        public readonly ?Connection $connection,
        public readonly array $processTableFields,
        public readonly array $steps,
        public readonly bool $disabled,
    ) {
    }
}
