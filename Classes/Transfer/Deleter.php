<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Transfer;

use Brotkrueml\JobRouterProcess\Exception\DeleteException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * @internal Only to be used within the jobrouter_process extension, not part of the public API
 */
class Deleter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private QueryBuilder $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function run(int $ageInDays): int
    {
        $this->logger->info('Starting clean up of old transfers');

        $maximumTimestampForDeletion = \time() - $ageInDays * 86400;

        $this->logger->debug('Maximum timestamp for deletion: ' . $maximumTimestampForDeletion);

        try {
            /** @var int $affectedRows */
            $affectedRows = $this->queryBuilder
                ->delete('tx_jobrouterprocess_domain_model_transfer')
                ->where(
                    $this->queryBuilder->expr()->eq(
                        'start_success',
                        $this->queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)
                    ),
                    $this->queryBuilder->expr()->lt(
                        'crdate',
                        $this->queryBuilder->createNamedParameter($maximumTimestampForDeletion, \PDO::PARAM_INT)
                    )
                )
                ->execute();
        } catch (\Exception $e) {
            $message = 'Error on clean up of old transfers: ' . $e->getMessage();
            $this->logger->error($message);
            throw new DeleteException($message, 1582133383, $e);
        }

        if ($affectedRows === 0) {
            $this->logger->info('No affected rows');
        } else {
            $this->logger->notice('Affected rows: ' . $affectedRows);
        }

        return $affectedRows;
    }
}
