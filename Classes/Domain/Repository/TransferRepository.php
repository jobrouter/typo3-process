<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Repository;

use Brotkrueml\JobRouterProcess\Domain\Dto\Transfer as TransferDto;
use Brotkrueml\JobRouterProcess\Domain\Entity\Transfer;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

class TransferRepository
{
    private const TABLE_NAME = 'tx_jobrouterprocess_domain_model_transfer';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {
    }

    /**
     * @return Transfer[]
     */
    public function findNotStarted(): array
    {
        $result = $this->connectionPool
            ->getConnectionForTable(self::TABLE_NAME)
            ->select(
                ['*'],
                self::TABLE_NAME,
                [
                    'start_success' => 0,
                ],
            );

        $transfers = [];
        while ($row = $result->fetchAssociative()) {
            $transfers[] = Transfer::fromArray($row);
        }

        return $transfers;
    }

    /**
     * @return Transfer[]
     */
    public function findErroneous(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $result = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('start_success', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->neq('start_message', $queryBuilder->createNamedParameter('')),
            )
            ->orderBy('crdate', 'ASC')
            ->executeQuery();

        $transfers = [];
        while ($row = $result->fetchAssociative()) {
            $transfers[] = Transfer::fromArray($row);
        }

        return $transfers;
    }

    public function add(TransferDto $data): int
    {
        // Default is string, so we need to define only those who are not a string
        $types = [
            'crdate' => Connection::PARAM_INT,
            'step_uid' => Connection::PARAM_INT,
            'priority' => Connection::PARAM_INT,
            'pool' => Connection::PARAM_INT,
            'encrypted_fields' => Connection::PARAM_INT,
        ];

        return $this->connectionPool
            ->getConnectionForTable(self::TABLE_NAME)
            ->insert(
                self::TABLE_NAME,
                $data->toArray(),
                $types,
            );
    }

    public function updateStartFields(int $uid, bool $success, int $date, string $message): int
    {
        return $this->connectionPool
            ->getConnectionForTable(self::TABLE_NAME)
            ->update(
                self::TABLE_NAME,
                [
                    'start_success' => ($success ? 1 : 0),
                    'start_date' => $date,
                    'start_message' => $message,
                ],
                [
                    'uid' => $uid,
                ],
                [
                    'start_success' => Connection::PARAM_INT,
                    'start_date' => Connection::PARAM_INT,
                    'start_message' => Connection::PARAM_STR,
                    'uid' => Connection::PARAM_INT,
                ],
            );
    }
}
