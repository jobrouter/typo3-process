<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class TransferRepository extends Repository
{
    /**
     * @return mixed[]|QueryResultInterface
     */
    public function findErroneousTransfers(): array|QueryResultInterface
    {
        $query = $this->createQuery();

        return $query
            ->matching(
                $query->logicalAnd([
                    $query->equals('startSuccess', 0),
                    $query->logicalNot(
                        $query->equals('startMessage', '')
                    ),
                ])
            )
            ->setOrderings([
                'crdate' => QueryInterface::ORDER_ASCENDING,
            ])
            ->execute();
    }
}
