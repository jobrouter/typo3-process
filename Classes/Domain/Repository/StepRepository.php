<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Repository;

use Brotkrueml\JobRouterProcess\Domain\Entity\Step;
use Brotkrueml\JobRouterProcess\Exception\StepNotFoundException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;

class StepRepository
{
    private const TABLE_NAME = 'tx_jobrouterprocess_domain_model_step';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {
    }

    /**
     * @return Step[]
     */
    public function findAll(bool $withDisabled = false): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        if ($withDisabled) {
            $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        }

        $result = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->orderBy('disabled')
            ->addOrderBy('handle')
            ->executeQuery();

        $steps = [];
        while ($row = $result->fetchAssociative()) {
            $steps[] = Step::fromArray($row);
        }

        return $steps;
    }

    public function findByUid(int $uid): Step
    {
        $row = $this->connectionPool
            ->getConnectionForTable(self::TABLE_NAME)
            ->select(
                ['*'],
                self::TABLE_NAME,
                [
                    'uid' => $uid,
                ],
            )
            ->fetchAssociative();

        if ($row === false) {
            throw StepNotFoundException::forUid($uid);
        }

        return Step::fromArray($row);
    }

    public function findByHandle(string $handle): Step
    {
        $row = $this->connectionPool
            ->getConnectionForTable(self::TABLE_NAME)
            ->select(
                ['*'],
                self::TABLE_NAME,
                [
                    'handle' => $handle,
                ],
            )
            ->fetchAssociative();

        if ($row === false) {
            throw StepNotFoundException::forHandle($handle);
        }

        return Step::fromArray($row);
    }
}
