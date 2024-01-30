<?php

use JobRouter\AddOn\Typo3Process\Controller\ListController;
use JobRouter\AddOn\Typo3Process\Extension;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

if ((new Typo3Version())->getMajorVersion() === 11) {
    ExtensionManagementUtility::addModule(
        'jobrouter',
        'process',
        '',
        '',
        [
            'routeTarget' => ListController::class . '::handleRequest',
            'access' => 'admin',
            'name' => Extension::MODULE_NAME,
            'iconIdentifier' => 'jobrouter-module-process',
            'labels' => Extension::LANGUAGE_PATH_BACKEND_MODULE,
            'workspaces' => 'online',
        ]
    );
}
