<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Domain\Repository;

use JobRouter\AddOn\Typo3Process\Domain\Entity\Step;
use JobRouter\AddOn\Typo3Process\Exception\StepNotFoundException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;

class StepRepository
{
    private const TABLE_NAME = 'tx_jobrouterprocess_domain_model_step';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {}

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

    /**
     * @return Step[]
     * @internal
     */
    public function findByProcessUid(int $processUid, bool $withDisabled = false): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        if ($withDisabled) {
            $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        }

        $result = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('process', $queryBuilder->createNamedParameter($processUid, Connection::PARAM_INT)),
            )
            ->orderBy('uid')
            ->executeQuery();

        $steps = [];
        while ($row = $result->fetchAssociative()) {
            $steps[] = Step::fromArray($row);
        }

        return $steps;
    }
}
