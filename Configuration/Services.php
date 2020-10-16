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
use Brotkrueml\JobRouterProcess\Command\CleanUpTransfersCommand;
use Brotkrueml\JobRouterProcess\Command\StartCommand;
use Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterProcess\EventListener\ToolbarItemProvider;
use Brotkrueml\JobRouterProcess\Transfer\Deleter;
use Brotkrueml\JobRouterProcess\Widgets\Provider\TransfersPerDayDataProvider;
use Brotkrueml\JobRouterProcess\Widgets\Provider\TransferStatusDataProvider;
use Brotkrueml\JobRouterProcess\Widgets\Provider\TransferTypeChartDataProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Backend\Backend\Event\SystemInformationToolbarCollectorEvent;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;
use TYPO3\CMS\Dashboard\Widgets\DoughnutChartWidget;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services
        ->load('Brotkrueml\JobRouterProcess\\', __DIR__ . '/../Classes/*');

    $services
        ->set('querybuilder.tx_jobrouterprocess_domain_model_transfer', QueryBuilder::class)
        ->factory([new Reference(ConnectionPool::class), 'getQueryBuilderForTable'])
        ->args(['tx_jobrouterprocess_domain_model_transfer']);

    $services
        ->set(CleanUpTransfersCommand::class)
        ->tag(
            'console.command',
            [
                'command' => 'jobrouter:process:cleanuptransfers',
                'schedulable' => true
            ]
        );

    $services->set(StartCommand::class)
        ->tag(
            'console.command',
            [
                'command' => 'jobrouter:process:start',
                'schedulable' => true
            ]
        );

    $services
        ->set(TransferRepository::class)
        ->arg('$queryBuilder', new Reference('querybuilder.tx_jobrouterprocess_domain_model_transfer'));

    $services
        ->set(ToolbarItemProvider::class)
        ->tag(
            'event.listener',
            [
                'identifier' => 'jobrouter-process/toolbar-item-provider',
                'event' => SystemInformationToolbarCollectorEvent::class
            ]
        );

    $services
        ->set(Deleter::class)
        ->arg('$queryBuilder', new Reference('querybuilder.tx_jobrouterprocess_domain_model_transfer'));

    if (ExtensionManagementUtility::isLoaded('dashboard')) {
        $parameters = $containerConfigurator->parameters();
        $parameters->set('jobrouter_process.widget.transfersPerDay.numberOfDays', 14);
        $parameters->set('jobrouter_process.widget.typeOfInstanceStarts.numberOfDays', 14);

        $services
            ->set('Brotkrueml\JobRouterProcess\Dashboard\Provider\TransfersPerDayDataProvider')
            ->call('setNumberOfDays', ['%jobrouter_process.widget.transfersPerDay.numberOfDays%']);

        $services
            ->set('Brotkrueml\JobRouterProcess\Dashboard\Provider\TransferTypeChartDataProvider')
            ->call('setNumberOfDays', ['%jobrouter_process.widget.typeOfInstanceStarts.numberOfDays%']);

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
    }
};
