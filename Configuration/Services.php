<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process;

use JobRouter\AddOn\Typo3Base\Widgets\TransferReportWidget;
use JobRouter\AddOn\Typo3Base\Widgets\TransferStatusWidget;
use JobRouter\AddOn\Typo3Process\Command\CleanUpTransfersCommand;
use JobRouter\AddOn\Typo3Process\Command\StartCommand;
use JobRouter\AddOn\Typo3Process\EventListener\ToolbarItemProvider;
use JobRouter\AddOn\Typo3Process\Widgets\Provider\TransferReportDataProvider;
use JobRouter\AddOn\Typo3Process\Widgets\Provider\TransfersPerDayDataProvider;
use JobRouter\AddOn\Typo3Process\Widgets\Provider\TransferStatusDataProvider;
use JobRouter\AddOn\Typo3Process\Widgets\Provider\TransferTypeChartDataProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Backend\Backend\Event\SystemInformationToolbarCollectorEvent;
use TYPO3\CMS\Dashboard\Dashboard;
use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;
use TYPO3\CMS\Dashboard\Widgets\DoughnutChartWidget;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $services = $containerConfigurator->services();
    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services
        ->load('JobRouter\AddOn\Typo3Process\\', '../Classes/*')
        ->exclude('../Classes/{Domain/Dto,Domain/Entity,Exception,Extension.php}');

    $services
        ->set(CleanUpTransfersCommand::class)
        ->tag(
            'console.command',
            [
                'command' => 'jobrouter:process:cleanuptransfers',
                'description' => 'Delete old entries in the transfer table',
                'schedulable' => true,
            ],
        );

    $services->set(StartCommand::class)
        ->tag(
            'console.command',
            [
                'command' => 'jobrouter:process:start',
                'description' => 'Start instances from data stored in the transfer table',
                'schedulable' => true,
            ],
        );

    $services
        ->set(ToolbarItemProvider::class)
        ->tag(
            'event.listener',
            [
                'identifier' => 'jobrouter-process/toolbar-item-provider',
                'event' => SystemInformationToolbarCollectorEvent::class,
            ],
        );

    if ($containerBuilder->hasDefinition(Dashboard::class)) {
        $parameters = $containerConfigurator->parameters();
        $parameters->set('jobrouter_process.widget.transfersPerDay.numberOfDays', 14);
        $parameters->set('jobrouter_process.widget.typeOfInstanceStarts.numberOfDays', 14);

        $services
            ->set(TransfersPerDayDataProvider::class)
            ->call('setNumberOfDays', ['%jobrouter_process.widget.transfersPerDay.numberOfDays%']);

        $services
            ->set(TransferTypeChartDataProvider::class)
            ->call('setNumberOfDays', ['%jobrouter_process.widget.typeOfInstanceStarts.numberOfDays%']);

        $services
            ->set('dashboard.widget.jobrouter_process.transfersPerDay')
            ->class(BarChartWidget::class)
            ->arg('$dataProvider', new Reference(TransfersPerDayDataProvider::class))
            ->arg(
                '$options',
                [
                    'refreshAvailable' => true,
                ],
            )
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
            ->set('dashboard.widget.jobrouter_process.typeOfInstanceStarts')
            ->class(DoughnutChartWidget::class)
            ->arg('$dataProvider', new Reference(TransferTypeChartDataProvider::class))
            ->arg(
                '$options',
                [
                    'numberOfDays' => Extension::WIDGET_TRANSFER_TYPE_DEFAULT_NUMBER_OF_DAYS,
                    'refreshAvailable' => true,
                ],
            )
            ->tag('dashboard.widget', [
                'identifier' => 'jobrouter_process.typeOfInstanceStartsDoughnut',
                'groupNames' => 'jobrouter',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.typeOfInstanceStarts.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.typeOfInstanceStarts.description',
                'iconIdentifier' => 'content-widget-chart-pie',
                'height' => 'medium',
            ]);

        $services
            ->set('dashboard.widget.jobrouter_process.statusOfInstanceStarts')
            ->class(TransferStatusWidget::class)
            ->arg('$dataProvider', new Reference(TransferStatusDataProvider::class))
            ->arg(
                '$options',
                [
                    'refreshAvailable' => true,
                ],
            )
            ->tag('dashboard.widget', [
                'identifier' => 'jobrouter_process.statusOfInstanceStarts',
                'groupNames' => 'jobrouter',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.statusOfInstanceStarts.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.statusOfInstanceStarts.description',
                'iconIdentifier' => 'content-widget-number',
                'height' => 'small',
            ]);

        $services
            ->set('dashboard.widget.jobrouter_process.transferReport')
            ->class(TransferReportWidget::class)
            ->arg('$dataProvider', new Reference(TransferReportDataProvider::class))
            ->arg(
                '$options',
                [
                    'refreshAvailable' => true,
                ],
            )
            ->tag('dashboard.widget', [
                'identifier' => 'jobrouter_process.transferReport',
                'groupNames' => 'jobrouter',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.transferReport.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.transferReport.description',
                'iconIdentifier' => 'content-widget-table',
                'height' => 'medium',
                'width' => 'large',
            ]);
    }
};
