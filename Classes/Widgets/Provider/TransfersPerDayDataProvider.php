<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Widgets\Provider;

use JobRouter\AddOn\Typo3Base\Extension as BaseExtension;
use JobRouter\AddOn\Typo3Process\Domain\Repository\TransferRepository;
use JobRouter\AddOn\Typo3Process\Extension;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

/**
 * @internal
 */
final class TransfersPerDayDataProvider implements ChartDataProviderInterface
{
    private int $numberOfDays = 14;
    /**
     * @var string[]
     */
    private array $labels = [];
    /**
     * @var int[]|mixed[]
     */
    private array $data = [];

    public function __construct(
        private readonly LanguageServiceFactory $languageServiceFactory,
        private readonly TransferRepository $transferRepository,
    ) {}

    public function setNumberOfDays(int $numberOfDays): void
    {
        $this->numberOfDays = $numberOfDays;
    }

    /**
     * @return array{labels: string[], datasets: array<int, array{label: string, backgroundColor: string, data: mixed[]}>}
     */
    public function getChartData(): array
    {
        $languageService = $this->languageServiceFactory->createFromUserPreferences($this->getBackendUser());

        $this->prepareData($languageService);

        return [
            'labels' => $this->labels,
            'datasets' => [
                [
                    'label' => $languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':numberOfStarts'),
                    'backgroundColor' => Extension::WIDGET_DEFAULT_CHART_COLOUR,
                    'data' => $this->data,
                ],
            ],
        ];
    }

    private function prepareData(LanguageService $languageService): void
    {
        $days = $this->transferRepository->countByDay($this->numberOfDays);

        $startDate = new \DateTime('now', new \DateTimeZone('UTC'));
        $startDate->setTime(0, 0);
        $startDate->sub(new \DateInterval(\sprintf('P%dD', $this->numberOfDays - 1)));

        $endDate = new \DateTime();

        for ($ts = $startDate->format('U'); $ts < $endDate->format('U'); $ts += 86400) {
            $this->labels[(int)$ts] = \date(
                $languageService->sL(BaseExtension::LANGUAGE_PATH_GENERAL . ':dateFormat'),
                (int)$ts,
            );

            $this->data[(int)$ts] = 0;
        }

        foreach ($days as $day) {
            $this->data[$day['day']] = $day['count'];
        }

        $this->labels = \array_values($this->labels);
        $this->data = \array_values($this->data);
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
