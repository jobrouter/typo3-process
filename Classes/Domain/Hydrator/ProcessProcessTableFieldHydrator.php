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
use Brotkrueml\JobRouterProcess\Domain\Repository\ProcessTableFieldRepository;

/**
 * @internal
 */
final class ProcessProcessTableFieldHydrator
{
    public function __construct(
        private readonly ProcessTableFieldRepository $processTableFieldRepository,
    ) {
    }

    public function hydrate(Process $process): Process
    {
        return $process->withProcessTableFields($this->processTableFieldRepository->findByProcessUid($process->uid));
    }
}
