<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Domain\Demand;

use JobRouter\AddOn\Typo3Connector\Domain\Repository\ConnectionRepository;
use JobRouter\AddOn\Typo3Connector\Exception\ConnectionNotFoundException;
use JobRouter\AddOn\Typo3Process\Domain\Entity\Process;
use JobRouter\AddOn\Typo3Process\Domain\Repository\ProcessTableFieldRepository;
use JobRouter\AddOn\Typo3Process\Domain\Repository\StepRepository;

/**
 * @internal
 */
final class ProcessDemandFactory
{
    public function __construct(
        private readonly ConnectionRepository $connectionRepository,
        private readonly ProcessTableFieldRepository $processTableFieldRepository,
        private readonly StepRepository $stepRepository,
    ) {}

    public function create(Process $process, bool $withDisabled = false): ProcessDemand
    {
        try {
            $connection = $this->connectionRepository->findByUid($process->connectionUid, $withDisabled);
        } catch (ConnectionNotFoundException) {
            $connection = null;
        }
        $processTableFields = $this->processTableFieldRepository->findByProcessUid($process->uid);
        $steps = $this->stepRepository->findByProcessUid($process->uid, $withDisabled);

        return new ProcessDemand(
            $process->uid,
            $process->name,
            $connection,
            $processTableFields,
            $steps,
            $process->disabled,
        );
    }

    /**
     * @param Process[] $processes
     * @return ProcessDemand[]
     */
    public function createMultiple(array $processes, bool $withDisabled = false): array
    {
        $demands = [];
        foreach ($processes as $process) {
            $demands[] = $this->create($process, $withDisabled);
        }

        return $demands;
    }
}
