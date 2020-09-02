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
use Brotkrueml\JobRouterProcess\Widgets\Provider\TransferStatusDataProvider;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class TransferStatusWidget implements WidgetInterface, AdditionalCssInterface
{
    /**
     * @var WidgetConfigurationInterface
     */
    private $configuration;

    /**
     * @var StandaloneView
     */
    private $view;

    /**
     * @var TransferStatusDataProvider
     */
    private $dataProvider;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        TransferStatusDataProvider $dataProvider,
        StandaloneView $view
    ) {
        $this->configuration = $configuration;
        $this->dataProvider = $dataProvider;
        $this->view = $view;
    }

    public function renderWidgetContent(): string
    {
        $this->view->setTemplate('Widget/TransferStatusWidget');
        $this->view->assignMultiple([
            'status' => $this->dataProvider->getStatus(),
            'configuration' => $this->configuration
        ]);

        return $this->view->render();
    }

    public function getCssFiles(): array
    {
        return [
            \sprintf('EXT:%s/Resources/Public/Css/widgets.css', Extension::KEY)
        ];
    }
}
