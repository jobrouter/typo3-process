<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Widgets\Provider;

use Brotkrueml\JobRouterBase\Extension as BaseExtension;
use Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterProcess\Extension;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

/**
 * @internal
 */
final class TransfersPerDayDataProvider implements ChartDataProviderInterface
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
     * @var int
     */
    private $numberOfDays = 14;

    public function __construct(LanguageService $languageService, TransferRepository $transferRepository)
    {
        $this->languageService = $languageService;
        $this->transferRepository = $transferRepository;
    }

    public function setNumberOfDays(int $numberOfDays): void
    {
        $this->numberOfDays = $numberOfDays;
    }

    public function getChartData(): array
    {
        [$labels, $data] = $this->prepareData();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $this->languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':numberOfStarts'),
                    'backgroundColor' => Extension::WIDGET_DEFAULT_CHART_COLOUR,
                    'data' => $data
                ]
            ]
        ];
    }

    private function prepareData(): array
    {
        $days = $this->transferRepository->countByDay($this->numberOfDays);

        $labels = [];
        $data = [];

        $startDate = new \DateTime('now', new \DateTimeZone('UTC'));
        $startDate->setTime(0, 0);

        $startDate->sub(new \DateInterval(\sprintf('P%dD', $this->numberOfDays - 1)));
        $endDate = new \DateTime();

        for ($ts = $startDate->format('U'); $ts < $endDate->format('U'); $ts += 86400) {
            $labels[(int)$ts] = \date(
                $this->languageService->sL(BaseExtension::LANGUAGE_PATH_GENERAL . ':dateFormat'),
                (int)$ts
            );

            $data[(int)$ts] = 0;
        }

        foreach ($days as $day) {
            $data[$day['day']] = $day['count'];
        }

        return [\array_values($labels), \array_values($data)];
    }
}
