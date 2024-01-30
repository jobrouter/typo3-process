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
use Brotkrueml\JobRouterProcess\Domain\Dto\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Exception\PrepareException;
use Psr\Log\LoggerInterface;

/**
 * @api
 */
class Preparer
{
    public function __construct(
        private readonly Encrypter $encrypter,
        private readonly LoggerInterface $logger,
        private readonly TransferRepository $transferRepository,
    ) {}

    public function store(Transfer $transfer): void
    {
        $encryptedTransfer = $this->encrypter->encryptIfConfigured($transfer);
        $numberOfRecords = $this->transferRepository->add($encryptedTransfer);
        if ($numberOfRecords === 0) {
            $message = 'Transfer record cannot be written';
            $this->logger->critical($message, $encryptedTransfer->toArray());
            throw PrepareException::forNotWritable();
        }
    }
}
