<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder;

use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * @internal
 */
class TransferRepository
{
    private const TABLE_NAME = 'tx_jobrouterprocess_domain_model_transfer';

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function countGroupByStartSuccess(): array
    {
        $queryBuilder = $this->createQueryBuilder();

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
        $queryBuilder = $this->createQueryBuilder();

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

    public function countTypes(int $numberOfDays): array
    {
        $queryBuilder = $this->createQueryBuilder();

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

    private function getDateBackFromToday(int $numberOfDays): \DateTimeInterface
    {
        $startDate = new \DateTime();
        $startDate->setTime(0, 0);
        $startDate->sub(new \DateInterval(\sprintf('P%dD', $numberOfDays - 1)));

        return $startDate;
    }

    public function countByDay(int $numberOfDays): array
    {
        $queryBuilder = $this->createQueryBuilder();

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
        $queryBuilder = $this->createQueryBuilder();

        $quotedCrdate = $queryBuilder->quoteIdentifier('crdate');

        return $queryBuilder
            ->selectLiteral(\sprintf('MIN(%s)', $quotedCrdate))
            ->from(self::TABLE_NAME)
            ->execute()
            ->fetchColumn() ?: 0;
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return clone $this->queryBuilder;
    }
}
