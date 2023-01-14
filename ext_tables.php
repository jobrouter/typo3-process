<?php

use Brotkrueml\JobRouterProcess\Controller\ListController;
use Brotkrueml\JobRouterProcess\Extension;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

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
