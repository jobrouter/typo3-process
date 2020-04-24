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
        $count = $this->queryBuilder
            ->count('*')
            ->from('tx_jobrouterprocess_domain_model_transfer')
            ->where('start_success = 0')
            ->andWhere('start_date > 0')
            ->execute()
            ->fetchColumn();

        if ($count === false) {
            return 0;
        }

        return $count;
    }
}
