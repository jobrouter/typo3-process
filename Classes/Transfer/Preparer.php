<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Transfer;

use Brotkrueml\JobRouterProcess\Crypt\Transfer\Encrypter;
use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Exception\PrepareException;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * @api
 */
class Preparer
{
    public function __construct(
        private readonly PersistenceManagerInterface $persistenceManager,
        private readonly Encrypter $encrypter,
        private readonly LoggerInterface $logger,
        private readonly TransferRepository $transferRepository,
    ) {
    }

    public function store(Transfer $transfer): void
    {
        try {
            $this->transferRepository->add($this->encrypter->encryptIfConfigured($transfer));
            $this->persistenceManager->persistAll();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw new PrepareException(
                'Transfer record cannot be written, reason: ' . $e->getMessage(),
                1581278897,
                $e,
            );
        }
    }
}
