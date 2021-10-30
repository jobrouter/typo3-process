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
use TYPO3\CMS\Extbase\Persistence\Repository;

class ProcessRepository extends Repository
{
    /**
     * @var array<string, string>
     * @phpstan-ignore-next-line
     */
    protected $defaultOrderings = [
        'disabled' => QueryInterface::ORDER_ASCENDING,
        'name' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @return mixed[]|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllWithHidden()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        return $query->execute();
    }
}
