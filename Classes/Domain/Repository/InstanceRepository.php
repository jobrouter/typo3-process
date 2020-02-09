<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Domain\Repository;

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class InstanceRepository extends Repository
{
    protected $defaultOrderings = [
        'disabled' => QueryInterface::ORDER_ASCENDING,
        'identifier' => QueryInterface::ORDER_ASCENDING,
    ];

    public function findAllWithHidden()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        return $query->execute();
    }
}
