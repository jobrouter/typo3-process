<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Dashboard\Provider;

use Brotkrueml\JobRouterProcess\Dashboard\TransferStatus;
use Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterProcess\Extension;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

/**
 * @internal
 */
final class TransferStatusChartDataProvider implements ChartDataProviderInterface
{
    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var TransferRepository
     */
    private $transferRepository;

    /**
     * @var TransferStatus
     */
    private $statusSuccessful;

    /**
     * @var TransferStatus
     */
    private $statusPending;

    /**
     * @var TransferStatus
     */
    private $statusFailed;

    public function __construct(LanguageService $languageService, TransferRepository $transferRepository)
    {
        $this->languageService = $languageService;
        $this->transferRepository = $transferRepository;
        $this->initialiseStatuses();
    }

    public function initialiseStatuses(): void
    {
        $this->statusSuccessful = new TransferStatus(
            $this->languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':status.successful'),
            '#4c7e3a'
        );
        $this->statusPending = new TransferStatus(
            $this->languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':status.pending'),
            '#fc3'
        );
        $this->statusFailed = new TransferStatus(
            $this->languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':status.failed'),
            '#a4276a'
        );
    }

    public function getChartData(): array
    {
        [$labels, $data, $colours] = $this->prepareData();

        return [
            'datasets' => [
                [
                    'backgroundColor' => $colours,
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function prepareData(): array
    {
        $this->calculateStatuses();

        $labels = [];
        $data = [];
        $colours = [];

        foreach (['successful', 'pending', 'failed'] as $status) {
            $labels[] = $this->{'status' . \ucfirst($status)}->getName();
            $data[] = $this->{'status' . \ucfirst($status)}->getCount();
            $colours[] = $this->{'status' . \ucfirst($status)}->getColour();
        }

        return [$labels, $data, $colours];
    }

    private function calculateStatuses(): void
    {
        $startSuccessCounts = $this->transferRepository->countGroupByStartSuccess();
        $toBeClassified = 0;
        foreach ($startSuccessCounts as $fields) {
            if ($fields['start_success'] === 0) {
                $toBeClassified = $fields['count'];
            } elseif ($fields['start_success'] === 1) {
                $this->statusSuccessful->setCount($fields['count']);
            }
        }

        if ($toBeClassified) {
            $this->statusFailed->setCount($this->transferRepository->countStartFailed());
            $this->statusPending->setCount($toBeClassified - $this->statusFailed->getCount());
        }
    }
}
