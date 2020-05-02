<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess;

use Brotkrueml\JobRouterProcess\Dashboard\Provider\TransfersPerDayDataProvider;
use Brotkrueml\JobRouterProcess\Dashboard\Provider\TransferStatusChartDataProvider;
use Brotkrueml\JobRouterProcess\Dashboard\Provider\TransferTypeChartDataProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;
use TYPO3\CMS\Dashboard\Widgets\DoughnutChartWidget;

return function (ContainerConfigurator $configurator): void {
    if (!ExtensionManagementUtility::isLoaded('dashboard')) {
        return;
    }

    $services = $configurator->services();

    $services
        ->set('dashboard.widget.brotkrueml.jobrouter_process.transfersPerDay')
        ->class(BarChartWidget::class)
        ->arg('$view', new Reference('dashboard.views.widget'))
        ->arg('$dataProvider', new Reference(TransfersPerDayDataProvider::class))
        ->tag('dashboard.widget', [
            'identifier' => 'jobrouter_process.transfersPerDayBar',
            'groupNames' => 'jobrouter',
            'title' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Dashboard.xlf:widgets.transfersPerDay.title',
            'description' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Dashboard.xlf:widgets.transfersPerDay.description',
            'iconIdentifier' => 'content-widget-chart-bar',
            'height' => 'medium',
            'width' => 'medium',
        ]);

    $services
        ->set('dashboard.widget.brotkrueml.jobrouter_process.typeOfInstanceStarts')
        ->class(DoughnutChartWidget::class)
        ->arg('$view', new Reference('dashboard.views.widget'))
        ->arg('$dataProvider', new Reference(TransferTypeChartDataProvider::class))
        ->tag('dashboard.widget', [
            'identifier' => 'jobrouter_process.typeOfInstanceStartsDoughnut',
            'groupNames' => 'jobrouter',
            'title' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Dashboard.xlf:widgets.typeOfInstanceStarts.title',
            'description' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Dashboard.xlf:widgets.typeOfInstanceStarts.description',
            'iconIdentifier' => 'content-widget-chart-pie',
            'height' => 'medium',
        ]);

    $services
        ->set('dashboard.widget.brotkrueml.jobrouter_process.statusOfInstanceStarts')
        ->class(DoughnutChartWidget::class)
        ->arg('$view', new Reference('dashboard.views.widget'))
        ->arg('$dataProvider', new Reference(TransferStatusChartDataProvider::class))
        ->tag('dashboard.widget', [
            'identifier' => 'jobrouter_process.statusOfInstanceStartsDoughnut',
            'groupNames' => 'jobrouter',
            'title' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Dashboard.xlf:widgets.statusOfInstanceStarts.title',
            'description' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Dashboard.xlf:widgets.statusOfInstanceStarts.description',
            'iconIdentifier' => 'content-widget-chart-pie',
            'height' => 'medium',
        ]);
};
