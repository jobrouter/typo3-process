<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Widgets;

use Brotkrueml\JobRouterProcess\Extension;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\DoughnutChartWidget;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfiguration;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class TypeOfInstanceStartsWidget extends DoughnutChartWidget
{
    public function __construct(
        WidgetConfigurationInterface $configuration,
        ChartDataProviderInterface $dataProvider,
        StandaloneView $view,
        array $options = []
    ) {
        $configuration = $this->addNumberOfDaysToTitle($configuration, $options);

        parent::__construct($configuration, $dataProvider, $view, null, $options);
    }

    private function addNumberOfDaysToTitle(
        WidgetConfigurationInterface $configuration,
        array $options
    ): WidgetConfigurationInterface {
        if ($options['numberOfDays'] === 1) {
            $titleSuffix = $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.typeOfInstanceStarts.title.lastDay');
        } else {
            $titleSuffix = \sprintf(
                $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.typeOfInstanceStarts.title.lastDays'),
                $options['numberOfDays']
            );
        }

        $title = \sprintf(
            '%s %s',
            $this->getLanguageService()->sL($configuration->getTitle()),
            $titleSuffix
        );

        return new WidgetConfiguration(
            $configuration->getIdentifier(),
            $configuration->getServiceName(),
            $configuration->getGroupNames(),
            $title,
            $configuration->getDescription(),
            $configuration->getIconIdentifier(),
            $configuration->getHeight(),
            $configuration->getWidth(),
            explode(' ', $configuration->getAdditionalCssClasses())
        );
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
