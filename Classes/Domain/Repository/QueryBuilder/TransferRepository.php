<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder;

use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @internal
 */
class TransferRepository
{
    private const TABLE_NAME = 'tx_jobrouterprocess_domain_model_transfer';

    private ConnectionPool $connectionPool;

    public function __construct(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
    }

    /**
     * @return mixed[]
     */
    public function countGroupByStartSuccess(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        return $queryBuilder
            ->select('start_success')
            ->addSelectLiteral('COUNT(*) AS ' . $queryBuilder->quoteIdentifier('count'))
            ->from(self::TABLE_NAME)
            ->groupBy('start_success')
            ->execute()
            ->fetchAll();
    }

    public function countStartFailed(): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $whereExpressions = [
            $queryBuilder->expr()->eq(
                'start_success',
                $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            ),
            $queryBuilder->expr()->gt(
                'start_date',
                $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            ),
        ];

        $count = $queryBuilder
            ->count('*')
            ->from(self::TABLE_NAME)
            ->where(...$whereExpressions)
            ->execute()
            ->fetchColumn();

        if ($count === false) {
            return 0;
        }

        return $count;
    }

    /**
     * @return mixed[]
     */
    public function countTypes(int $numberOfDays): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $startDate = $this->getDateBackFromToday($numberOfDays);

        return $queryBuilder
            ->select('type')
            ->addSelectLiteral('COUNT(*) AS ' . $queryBuilder->quoteIdentifier('count'))
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->gte(
                    'crdate',
                    $queryBuilder->createNamedParameter($startDate->format('U'), \PDO::PARAM_INT)
                )
            )
            ->groupBy('type')
            ->orderBy('count', 'DESC')
            ->execute()
            ->fetchAll();
    }

    private function getDateBackFromToday(int $numberOfDays): \DateTime
    {
        $startDate = new \DateTime();
        $startDate->setTime(0, 0);
        $startDate->sub(new \DateInterval(\sprintf('P%dD', $numberOfDays - 1)));

        return $startDate;
    }

    /**
     * @return mixed[]
     */
    public function countByDay(int $numberOfDays): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $startDate = $this->getDateBackFromToday($numberOfDays);

        $quotedCrdate = $queryBuilder->quoteIdentifier('crdate');
        $literal = \sprintf(
            '%s - (%s %% 86400) AS %s',
            $quotedCrdate,
            $quotedCrdate,
            $queryBuilder->quoteIdentifier('day')
        );

        return $queryBuilder
            ->selectLiteral($literal)
            ->addSelectLiteral('COUNT(*) AS ' . $queryBuilder->quoteIdentifier('count'))
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->gte(
                    'crdate',
                    $queryBuilder->createNamedParameter($startDate->format('U'), \PDO::PARAM_INT)
                )
            )
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->execute()
            ->fetchAll();
    }

    public function findFirstCreationDate(): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $quotedCrdate = $queryBuilder->quoteIdentifier('crdate');

        return $queryBuilder
            ->selectLiteral(\sprintf('MIN(%s)', $quotedCrdate))
            ->from(self::TABLE_NAME)
            ->execute()
            ->fetchColumn() ?: 0;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findForDeletion(int $maximumTimestamp): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        return $queryBuilder
            ->select('t.uid', 't.processtable', 'p.uid AS process_uid')
            ->from(self::TABLE_NAME, 't')
            ->leftJoin(
                't',
                'tx_jobrouterprocess_domain_model_step',
                's',
                $queryBuilder->expr()->eq('t.step_uid', $queryBuilder->quoteIdentifier('s.uid'))
            )
            ->leftJoin(
                's',
                'tx_jobrouterprocess_domain_model_process',
                'p',
                $queryBuilder->expr()->eq('s.process', $queryBuilder->quoteIdentifier('p.uid'))
            )
            ->where(
                $queryBuilder->expr()->eq(
                    't.start_success',
                    $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->lt(
                    't.crdate',
                    $queryBuilder->createNamedParameter($maximumTimestamp, \PDO::PARAM_INT)
                )
            )
            ->orderBy('t.uid')
            ->execute()
            ->fetchAllAssociative();
    }

    public function delete(int $uid): int
    {
        $connection = $this->connectionPool->getConnectionForTable(self::TABLE_NAME);

        return $connection
            ->delete(
                self::TABLE_NAME,
                [
                    'uid' => $uid,
                    // This is just a failsafe, so no transfer which was not sent is deleted
                    'start_success' => 1,
                ],
                [
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT,
                ]
            );
    }
}
