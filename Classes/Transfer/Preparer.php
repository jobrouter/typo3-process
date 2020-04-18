<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Transfer;

use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Exception\PrepareException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * @api
 */
class Preparer implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var PersistenceManager */
    private $persistenceManager;

    /** @var TransferRepository */
    private $transferRepository;

    public function __construct(PersistenceManager $persistenceManager = null, TransferRepository $transferRepository = null)
    {
        if ($persistenceManager === null || $transferRepository === null) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $this->persistenceManager = $objectManager->get(PersistenceManager::class);
            $this->transferRepository = $objectManager->get(TransferRepository::class);
            return;
        }

        $this->persistenceManager = $persistenceManager;
        $this->transferRepository = $transferRepository;
    }

    public function store(Transfer $transfer): void
    {
        try {
            $this->transferRepository->add($transfer);
            $this->persistenceManager->persistAll();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw new PrepareException('Transfer record cannot be written', 1581278897, $e);
        }
    }
}
