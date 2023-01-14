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
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * @internal
 */
class BackendController extends ActionController
{
    private const MODULE_NAME = 'jobrouter_JobRouterProcessLinks';

    /**
     * @var class-string<\TYPO3\CMS\Backend\View\BackendTemplateView>
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var ModuleTemplate
     * @noRector
     */
    private $moduleTemplate;

    /**
     * @var ButtonBar
     * @noRector
     */
    private $buttonBar;

    public function __construct(
        private readonly IconFactory $iconFactory,
        private readonly LanguageService $languageService,
        private readonly ProcessRepository $processRepository,
        private readonly StepRepository $stepRepository
    ) {
    }

    protected function initializeView(ViewInterface $view): void
    {
        parent::initializeView($view);

        $this->moduleTemplate = $this->view->getModuleTemplate();

        $this->buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $this->createRefreshHeaderButton();
        $this->createShortcutHeaderButton();
    }

    public function listAction(): void
    {
        $pageRenderer = $this->moduleTemplate->getPageRenderer();
        $pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/JobrouterProcess/ProcessTableFieldsToggler'
        );

        $processes = $this->processRepository->findAllWithHidden();
        $steps = $this->stepRepository->findAllWithHidden();

        $this->view->assign('processes', $processes);
        $this->view->assign('steps', $steps);
    }

    protected function createRefreshHeaderButton(): void
    {
        $title = $this->languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload');

        $refreshButton = $this->buttonBar->makeLinkButton()
            ->setHref(GeneralUtility::getIndpEnv('REQUEST_URI'))
            ->setTitle($title)
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $this->buttonBar->addButton($refreshButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    protected function createShortcutHeaderButton(): void
    {
        if (! $this->getBackendUser()->mayMakeShortcut()) {
            return;
        }

        $label = $this->languageService->sL(
            \sprintf(
                'LLL:EXT:%s/Resources/Private/Language/BackendModule.xlf:heading_text',
                Extension::KEY
            )
        );

        $shortcutButton = $this->buttonBar->makeShortcutButton()
            ->setModuleName(self::MODULE_NAME)
            ->setGetVariables(['route', 'module', 'id'])
            ->setDisplayName($label);
        $this->buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
