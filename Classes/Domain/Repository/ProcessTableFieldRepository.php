<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Domain\Repository;

use JobRouter\AddOn\Typo3Process\Domain\Entity\ProcessTableField;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @internal
 */
class ProcessTableFieldRepository
{
    private const TABLE_NAME = 'tx_jobrouterprocess_domain_model_processtablefield';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {}

    /**
     * @return ProcessTableField[]
     */
    public function findByProcessUid(int $uid): array
    {
        $result = $this->connectionPool
            ->getConnectionForTable(self::TABLE_NAME)
            ->select(
                ['*'],
                self::TABLE_NAME,
                [
                    'process_uid' => $uid,
                ],
                orderBy: [
                    'sorting' => 'asc',
                ],
            );

        $fields = [];
        while ($row = $result->fetchAssociative()) {
            $fields[] = ProcessTableField::fromArray($row);
        }

        return $fields;
    }
}
