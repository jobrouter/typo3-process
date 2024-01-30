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

/**
 * @internal
 */
class TransferRepository
{
    private const TABLE_NAME = 'tx_jobrouterprocess_domain_model_transfer';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {}

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

    /**
     * @return array<int,array<string,mixed>>
     */
    public function countGroupByStartSuccess(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        return $queryBuilder
            ->select('start_success')
            ->addSelectLiteral('COUNT(*) AS ' . $queryBuilder->quoteIdentifier('count'))
            ->from(self::TABLE_NAME)
            ->groupBy('start_success')
            ->orderBy('start_success')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function countStartFailed(): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $whereExpressions = [
            $queryBuilder->expr()->eq(
                'start_success',
                $queryBuilder->createNamedParameter(0, Connection::PARAM_INT),
            ),
            $queryBuilder->expr()->gt(
                'start_date',
                $queryBuilder->createNamedParameter(0, Connection::PARAM_INT),
            ),
        ];

        return $queryBuilder
            ->count('*')
            ->from(self::TABLE_NAME)
            ->where(...$whereExpressions)
            ->executeQuery()
            ->fetchOne() ?: 0;
    }

    /**
     * @return array<int,array<string,mixed>>
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
                    $queryBuilder->createNamedParameter($startDate->format('U'), Connection::PARAM_INT),
                ),
            )
            ->groupBy('type')
            ->orderBy('count', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    private function getDateBackFromToday(int $numberOfDays): \DateTime
    {
        $startDate = new \DateTime();
        $startDate->setTime(0, 0);
        $startDate->sub(new \DateInterval(\sprintf('P%dD', $numberOfDays - 1)));

        return $startDate;
    }

    /**
     * @return array<int,array<string,mixed>>
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
            $queryBuilder->quoteIdentifier('day'),
        );

        return $queryBuilder
            ->selectLiteral($literal)
            ->addSelectLiteral('COUNT(*) AS ' . $queryBuilder->quoteIdentifier('count'))
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->gte(
                    'crdate',
                    $queryBuilder->createNamedParameter($startDate->format('U'), Connection::PARAM_INT),
                ),
            )
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function findFirstCreationDate(): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $quotedCrdate = $queryBuilder->quoteIdentifier('crdate');

        return $queryBuilder
            ->selectLiteral(\sprintf('MIN(%s)', $quotedCrdate))
            ->from(self::TABLE_NAME)
            ->executeQuery()
            ->fetchOne() ?: 0;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findForDeletion(int $maximumTimestamp): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        return $queryBuilder
            ->select('t.uid', 't.processtable', 't.encrypted_fields', 'p.uid AS process_uid')
            ->from(self::TABLE_NAME, 't')
            ->leftJoin(
                't',
                'tx_jobrouterprocess_domain_model_step',
                's',
                $queryBuilder->expr()->eq('t.step_uid', $queryBuilder->quoteIdentifier('s.uid')),
            )
            ->leftJoin(
                's',
                'tx_jobrouterprocess_domain_model_process',
                'p',
                $queryBuilder->expr()->eq('s.process', $queryBuilder->quoteIdentifier('p.uid')),
            )
            ->where(
                $queryBuilder->expr()->eq(
                    't.start_success',
                    $queryBuilder->createNamedParameter(1, Connection::PARAM_INT),
                ),
                $queryBuilder->expr()->lt(
                    't.crdate',
                    $queryBuilder->createNamedParameter($maximumTimestamp, Connection::PARAM_INT),
                ),
            )
            ->orderBy('t.uid')
            ->executeQuery()
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
                    // This is just a failsafe, so no transfer is deleted, which was not sent,
                    'start_success' => 1,
                ],
                [
                    Connection::PARAM_INT,
                    Connection::PARAM_INT,
                ],
            );
    }
}
