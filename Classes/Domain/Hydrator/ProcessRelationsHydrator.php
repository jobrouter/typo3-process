<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Hydrator;

use Brotkrueml\JobRouterProcess\Domain\Entity\Process;

/**
 * @internal
 */
final class ProcessRelationsHydrator
{
    public function __construct(
        private readonly ProcessConnectionHydrator $connectionHydrator,
        private readonly ProcessProcesstablefieldsHydrator $processtablefieldsHydrator,
    ) {
    }

    public function hydrate(Process $process): Process
    {
        return $this->connectionHydrator->hydrate(
            $this->processtablefieldsHydrator->hydrate($process),
        );
    }

    /**
     * @param Process[] $processes
     * @return Process[]
     */
    public function hydrateMultiple(array $processes): array
    {
        return \array_map($this->hydrate(...), $processes);
    }
}
