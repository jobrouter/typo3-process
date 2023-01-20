<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Hydrator;

use Brotkrueml\JobRouterProcess\Domain\Entity\Step;
use Brotkrueml\JobRouterProcess\Domain\Repository\ProcessRepository;

/**
 * @internal
 */
final class StepProcessHydrator
{
    public function __construct(
        private readonly ProcessRepository $processRepository,
    ) {
    }

    public function hydrate(Step $step): Step
    {
        return $step->withProcess($this->processRepository->findByUid($step->processUid, true));
    }

    /**
     * @param Step[] $steps
     * @return Step[]
     */
    public function hydrateMultiple(array $steps): array
    {
        return \array_map($this->hydrate(...), $steps);
    }
}
