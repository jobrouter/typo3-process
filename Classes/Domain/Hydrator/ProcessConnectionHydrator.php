<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Hydrator;

use Brotkrueml\JobRouterConnector\Domain\Entity\Connection;
use Brotkrueml\JobRouterConnector\Domain\Repository\ConnectionRepository;
use Brotkrueml\JobRouterConnector\Exception\ConnectionNotFoundException;
use Brotkrueml\JobRouterProcess\Domain\Entity\Process;

/**
 * @internal
 */
final class ProcessConnectionHydrator
{
    /**
     * @var array<int, Connection|null>
     */
    private array $connectionsCache = [];

    public function __construct(
        private readonly ConnectionRepository $connectionRepository,
    ) {
    }

    public function hydrate(Process $process, bool $withDisabled = false): Process
    {
        if (! isset($this->connectionsCache[$process->connectionUid])) {
            try {
                $this->connectionsCache[$process->connectionUid] = $this->connectionRepository->findByUid($process->connectionUid, $withDisabled);
            } catch (ConnectionNotFoundException) {
                $this->connectionsCache[$process->connectionUid] = null;
            }
        }

        if (! $this->connectionsCache[$process->connectionUid] instanceof Connection) {
            return $process;
        }

        return $process->withConnection($this->connectionsCache[$process->connectionUid]);
    }
}
