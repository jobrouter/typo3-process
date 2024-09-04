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
use JobRouter\AddOn\Typo3Process\EventListener\ToolbarItemProvider;
use JobRouter\AddOn\Typo3Process\Widgets\Provider\TransferReportDataProvider;
use JobRouter\AddOn\Typo3Process\Widgets\Provider\TransferStatusDataProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Dashboard\Dashboard;

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
        ->set(ToolbarItemProvider::class)
        ->tag(
            'event.listener',
            [
                'identifier' => 'jobrouter-process/toolbar-item-provider',
            ],
        );

    if ($containerBuilder->hasDefinition(Dashboard::class)) {
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
