<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Dashboard\Provider;

use Brotkrueml\JobRouterProcess\Extension;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

/**
 * @internal
 */
final class TransferTypeChartDataProvider implements ChartDataProviderInterface
{
    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(LanguageService $languageService, QueryBuilder $queryBuilder)
    {
        $this->languageService = $languageService;
        $this->queryBuilder = $queryBuilder;
    }

    public function getChartData(): array
    {
        [$labels, $data] = $this->prepareData();

        return [
            'datasets' => [
                [
                    'backgroundColor' => $this->getChartColours(\count($data)),
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function prepareData(): array
    {
        $types = $this->countTypes();
        $unknownLabel = $this->languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':unknown');

        $labels = [];
        $data = [];
        foreach ($types as $type) {
            $labels[] = $type['type'] ?: $unknownLabel;
            $data[] = $type['count'];
        }

        return [$labels, $data];
    }

    private function countTypes(): array
    {
        return $this->queryBuilder
            ->selectLiteral(
                $this->queryBuilder->quoteIdentifier('type'),
                'COUNT(1) AS ' . $this->queryBuilder->quoteIdentifier('count')
            )
            ->from('tx_jobrouterprocess_domain_model_transfer')
            ->groupBy('type')
            ->orderBy('count', 'DESC')
            ->execute()
            ->fetchAll();
    }

    private function getChartColours(int $count): array
    {
        $chartColours = \array_merge(['#fc3'], WidgetApi::getDefaultChartColors());

        while (\count($chartColours) < $count) {
            $chartColours = \array_merge($chartColours, $chartColours);
        }

        return \array_slice($chartColours, 0, $count);
    }
}
