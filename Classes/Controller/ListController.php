<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Controller;

use Brotkrueml\JobRouterProcess\Domain\Repository\ProcessRepository;
use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
use Brotkrueml\JobRouterProcess\Extension;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

final class ListController
{
    private ModuleTemplate $moduleTemplate;
    private StandaloneView $view;

    public function __construct(
        private readonly IconFactory $iconFactory,
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly PageRenderer $pageRenderer,
        private readonly ProcessRepository $processRepository,
        private readonly StepRepository $stepRepository,
        private readonly UriBuilder $uriBuilder,
    ) {
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($request);

        $this->pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/JobrouterProcess/ProcessTableFieldsToggler',
        );

        $this->initializeView();

        $processes = $this->processRepository->findAllWithHidden();
        $steps = $this->stepRepository->findAllWithHidden();
        $this->view->assignMultiple([
            'processes' => $processes,
            'steps' => $steps,
        ]);

        $this->configureDocHeader(
            $request->getAttribute('normalizedParams')?->getRequestUri() ?? '',
            \count($processes) > 0,
        );

        $this->moduleTemplate->setContent($this->view->render());

        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    private function initializeView(): void
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplate('List');
        $this->view->setTemplateRootPaths(['EXT:' . Extension::KEY . '/Resources/Private/Templates/Backend']);
    }

    private function configureDocHeader(string $requestUri, bool $hasProcesses): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $newProcessButton = $buttonBar->makeLinkButton()
            ->setHref((string)$this->uriBuilder->buildUriFromRoute(
                'record_edit',
                [
                    'edit' => [
                        'tx_jobrouterprocess_domain_model_process' => ['new'],
                    ],
                    'returnUrl' => (string)$this->uriBuilder->buildUriFromRoute(Extension::MODULE_NAME),
                ],
            ))
            ->setTitle($this->getLanguageService()->sL(Extension::LANGUAGE_PATH_BACKEND_MODULE . ':action.add_process'))
            ->setShowLabelText(true)
            ->setIcon($this->iconFactory->getIcon('actions-add', Icon::SIZE_SMALL));
        $buttonBar->addButton($newProcessButton, buttonGroup: 10);

        if ($hasProcesses) {
            $newStepButton = $buttonBar->makeLinkButton()
                ->setHref((string)$this->uriBuilder->buildUriFromRoute(
                    'record_edit',
                    [
                        'edit' => [
                            'tx_jobrouterprocess_domain_model_step' => ['new'],
                        ],
                        'returnUrl' => (string)$this->uriBuilder->buildUriFromRoute(Extension::MODULE_NAME),
                    ],
                ))
                ->setTitle($this->getLanguageService()->sL(Extension::LANGUAGE_PATH_BACKEND_MODULE . ':action.add_step'))
                ->setShowLabelText(true)
                ->setIcon($this->iconFactory->getIcon('actions-add', Icon::SIZE_SMALL));
            $buttonBar->addButton($newStepButton, buttonGroup: 20);
        }

        $reloadButton = $buttonBar->makeLinkButton()
            ->setHref($requestUri)
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($reloadButton, ButtonBar::BUTTON_POSITION_RIGHT);

        if ($this->getBackendUser()->mayMakeShortcut()) {
            $shortcutButton = $buttonBar->makeShortcutButton()
                ->setRouteIdentifier(Extension::MODULE_NAME)
                ->setDisplayName($this->getLanguageService()->sL(Extension::LANGUAGE_PATH_BACKEND_MODULE . ':heading_text'));
            $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
