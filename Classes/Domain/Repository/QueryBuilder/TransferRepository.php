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
        return $this->queryBuilder
            ->select('start_success')
            ->addSelectLiteral('COUNT(*) AS ' . $this->queryBuilder->quoteIdentifier('count'))
            ->from('tx_jobrouterprocess_domain_model_transfer')
            ->groupBy('start_success')
            ->execute()
            ->fetchAll();
    }

    public function countStartFailed(): int
    {
        $whereExpressions = [
            $this->queryBuilder->expr()->eq(
                'start_success',
                $this->queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            ),
            $this->queryBuilder->expr()->gt(
                'start_date',
                $this->queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            ),
        ];

        $count = $this->queryBuilder
            ->count('*')
            ->from('tx_jobrouterprocess_domain_model_transfer')
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
        $startDate = new \DateTime();
        $startDate->setTime(0, 0);
        $startDate->sub(new \DateInterval(\sprintf('P%dD', $numberOfDays - 1)));

        return $this->queryBuilder
            ->select('type')
            ->addSelectLiteral('COUNT(*) AS ' . $this->queryBuilder->quoteIdentifier('count'))
            ->from('tx_jobrouterprocess_domain_model_transfer')
            ->where(
                $this->queryBuilder->expr()->gte(
                    'crdate',
                    $this->queryBuilder->createNamedParameter($startDate->format('U'), \PDO::PARAM_INT)
                )
            )
            ->groupBy('type')
            ->orderBy('count', 'DESC')
            ->execute()
            ->fetchAll();
    }
}
