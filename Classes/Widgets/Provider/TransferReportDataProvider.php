<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Widgets\Provider;

use JobRouter\AddOn\Typo3Base\Domain\Dto\TransferReportItem;
use JobRouter\AddOn\Typo3Process\Domain\Repository\TransferRepository;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

/**
 * @internal
 */
final class TransferReportDataProvider implements ListDataProviderInterface
{
    public function __construct(
        private readonly TransferRepository $transferRepository,
    ) {}

    /**
     * @return TransferReportItem[]
     */
    public function getItems(): array
    {
        $transfers = $this->transferRepository->findErroneous();

        $items = [];
        foreach ($transfers as $transfer) {
            $items[] = new TransferReportItem(
                $transfer->crdate,
                $transfer->startMessage,
                $transfer->correlationId,
            );
        }

        return $items;
    }
}
