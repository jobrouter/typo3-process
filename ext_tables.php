<?php
defined('TYPO3_MODE') || die('Access denied.');

(function () {
    TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Brotkrueml.JobRouterProcess',
        'jobrouter',
        'jobrouterprocess',
        '',
        [
            'Backend' => 'list',
        ],
        [
            'access' => 'admin',
            'icon' => 'EXT:' . Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/jobrouter-process-module.svg',
            'labels' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_BACKEND_MODULE,
        ]
    );

    $iconRegistry = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
    $iconRegistry->registerIcon(
        'action-open-designer',
        TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:' . Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/action-open-designer.svg']
    );
    $iconRegistry->registerIcon(
        'jobrouter-process-toolbar',
        TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:' . Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/jobrouter-process-toolbar.svg']
    );
    foreach (['failed', 'pending', 'successful'] as $status) {
        $iconRegistry->registerIcon(
            'jobrouter-process-status-' . $status,
            TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            [
                'source' => \sprintf(
                    'EXT:%s/Resources/Public/Icons/status-%s.svg',
                    Brotkrueml\JobRouterProcess\Extension::KEY,
                    $status
                )
            ]
        );
    }

    TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
        module.tx_dashboard {
            view {
                templateRootPaths.1598898800 = EXT:jobrouter_process/Resources/Private/Templates/
                partialRootPaths.1598898800 = EXT:jobrouter_process/Resources/Private/Partials/
                layoutRootPaths.1598898800 = EXT:jobrouter_process/Resources/Private/Layouts/
            }
        }'
    );
})();
