<?php
defined('TYPO3') || die();

(static function () {
    if ((new TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion() === 10) {
        // Since TYPO3 v11.4 icons can be registered in Configuration/Icons.php
        /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
        $iconRegistry = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $iconRegistry->registerIcon(
            'jobrouter-module-process',
            TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:' . Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/jobrouter-process-module.svg']
        );
        $iconRegistry->registerIcon(
            'jobrouter-process-toolbar',
            TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:' . Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/jobrouter-process-toolbar.svg']
        );
        $iconRegistry->registerIcon(
            'jobrouter-action-open-designer',
            TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:' . Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/action-open-designer.svg']
        );
    }

    TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'JobRouterProcess',
        'jobrouter',
        'links',
        '',
        [
            Brotkrueml\JobRouterProcess\Controller\BackendController::class => 'list',
        ],
        [
            'access' => 'admin',
            'iconIdentifier' => 'jobrouter-module-process',
            'labels' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_BACKEND_MODULE,
        ]
    );
})();
