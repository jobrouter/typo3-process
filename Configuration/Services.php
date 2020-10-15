<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess;

use Brotkrueml\JobRouterBase\Widgets\TransferStatusWidget;
use Brotkrueml\JobRouterProcess\Widgets\Provider\TransfersPerDayDataProvider;
use Brotkrueml\JobRouterProcess\Widgets\Provider\TransferStatusDataProvider;
use Brotkrueml\JobRouterProcess\Widgets\Provider\TransferTypeChartDataProvider;
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
            'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.transfersPerDay.title',
            'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.transfersPerDay.description',
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
            'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.typeOfInstanceStarts.title',
            'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.typeOfInstanceStarts.description',
            'iconIdentifier' => 'content-widget-chart-pie',
            'height' => 'medium',
        ]);

    $services
        ->set('dashboard.widget.brotkrueml.jobrouter_process.statusOfInstanceStarts')
        ->class(TransferStatusWidget::class)
        ->arg('$view', new Reference('dashboard.views.widget'))
        ->arg('$dataProvider', new Reference(TransferStatusDataProvider::class))
        ->tag('dashboard.widget', [
            'identifier' => 'jobrouter_process.statusOfInstanceStarts',
            'groupNames' => 'jobrouter',
            'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.statusOfInstanceStarts.title',
            'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.statusOfInstanceStarts.description',
            'iconIdentifier' => 'content-widget-number',
            'height' => 'small',
        ]);
};
