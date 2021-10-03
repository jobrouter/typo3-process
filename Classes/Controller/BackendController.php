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
use TYPO3\CMS\Backend\Routing\UriBuilder;
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
    private const MODULE_NAME = 'jobrouter_JobRouterProcessJobrouterprocess';

    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var ProcessRepository
     */
    private $processRepository;

    /**
     * @var StepRepository
     */
    private $stepRepository;

    /**
     * @var IconFactory
     */
    private $iconFactory;

    /**
     * @var UriBuilder
     */
    private $backendUriBuilder;

    /**
     * @var ModuleTemplate
     */
    private $moduleTemplate;

    /**
     * @var ButtonBar
     */
    private $buttonBar;

    public function injectProcessRepository(ProcessRepository $processRepository): void
    {
        $this->processRepository = $processRepository;
    }

    public function injectStepRepository(StepRepository $stepRepository): void
    {
        $this->stepRepository = $stepRepository;
    }

    public function injectIconFactory(IconFactory $iconFactory): void
    {
        $this->iconFactory = $iconFactory;
    }

    public function injectUriBuilder(UriBuilder $uriBuilder): void
    {
        $this->backendUriBuilder = $uriBuilder;
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
        $title = $this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload');

        $refreshButton = $this->buttonBar->makeLinkButton()
            ->setHref(GeneralUtility::getIndpEnv('REQUEST_URI'))
            ->setTitle($title)
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $this->buttonBar->addButton($refreshButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    protected function createShortcutHeaderButton(): void
    {
        if ($this->getBackendUser()->mayMakeShortcut()) {
            $shortcutButton = $this->buttonBar->makeShortcutButton()
                ->setModuleName(self::MODULE_NAME)
                ->setGetVariables(['route', 'module', 'id'])
                ->setDisplayName('Shortcut');
            $this->buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
