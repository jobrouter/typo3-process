<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Widgets\Provider;

use JobRouter\AddOn\Typo3Process\Domain\Repository\TransferRepository;
use JobRouter\AddOn\Typo3Process\Extension;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

/**
 * @internal
 */
final class TransferTypeChartDataProvider implements ChartDataProviderInterface
{
    private int $numberOfDays = Extension::WIDGET_TRANSFER_TYPE_DEFAULT_NUMBER_OF_DAYS;
    /**
     * @var mixed[]
     */
    private array $labels = [];
    /**
     * @var mixed[]
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
     * @return array{datasets: array<int, array{backgroundColor: string[], data: mixed[]}>, labels: mixed[]}
     */
    public function getChartData(): array
    {
        $this->prepareData();

        return [
            'datasets' => [
                [
                    'backgroundColor' => $this->getChartColours(\count($this->data)),
                    'data' => $this->data,
                ],
            ],
            'labels' => $this->labels,
        ];
    }

    private function prepareData(): void
    {
        $languageService = $this->languageServiceFactory->createFromUserPreferences($this->getBackendUser());
        $unknownLabel = $languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':unknown');

        $types = $this->transferRepository->countTypes($this->numberOfDays);
        foreach ($types as $type) {
            $this->labels[] = $type['type'] ?: $unknownLabel;
            $this->data[] = $type['count'];
        }
    }

    /**
     * @return string[]
     */
    private function getChartColours(int $count): array
    {
        $chartColours = [Extension::WIDGET_DEFAULT_CHART_COLOUR, ...WidgetApi::getDefaultChartColors()];

        while (\count($chartColours) < $count) {
            $chartColours = [...$chartColours, ...$chartColours];
        }

        return \array_slice($chartColours, 0, $count);
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
