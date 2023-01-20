<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Repository;

use Brotkrueml\JobRouterProcess\Domain\Entity\Process;
use Brotkrueml\JobRouterProcess\Exception\ProcessNotFoundException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;

class ProcessRepository
{
    private const TABLE_NAME = 'tx_jobrouterprocess_domain_model_process';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {
    }

    /**
     * @return Process[]
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
            ->addOrderBy('name')
            ->executeQuery();

        $processes = [];
        while ($row = $result->fetchAssociative()) {
            $processes[] = Process::fromArray($row);
        }

        return $processes;
    }

    public function findByUid(int $uid, bool $withDisabled = false): Process
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        if ($withDisabled) {
            $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        }

        $row = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)),
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw ProcessNotFoundException::forUid($uid);
        }

        return Process::fromArray($row);
    }
}
