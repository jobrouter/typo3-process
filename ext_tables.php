<?php
defined('TYPO3_MODE') || die('Access denied.');

(function ($extensionKey = 'jobrouter_process') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Brotkrueml.JobRouterProcess',
        'jobrouter',
        'jobrouterprocess',
        '',
        [
            'Backend' => 'list',
        ],
        [
            'access' => 'admin',
            'icon' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/jobrouter-process-module.svg',
            'labels' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/BackendModule.xlf',
        ]
    );

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
    $iconRegistry->registerIcon(
        'action-open-designer',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/action-open-designer.svg']
    );
    $iconRegistry->registerIcon(
        'jobrouter-process-toolbar',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/jobrouter-process-toolbar.svg']
    );

    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class)
        ->connect(
            \TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem::class,
            'getSystemInformation',
            \Brotkrueml\JobRouterProcess\SystemInformation\ToolbarItemProvider::class,
            'getItem'
        );
})();
