<?php

use Brotkrueml\JobRouterProcess\Controller\BackendController;
use Brotkrueml\JobRouterProcess\Extension;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

ExtensionUtility::registerModule(
    'JobRouterProcess',
    'jobrouter',
    'links',
    '',
    [
        BackendController::class => 'list',
    ],
    [
        'access' => 'admin',
        'iconIdentifier' => 'jobrouter-module-process',
        'labels' => Extension::LANGUAGE_PATH_BACKEND_MODULE,
        'workspaces' => 'online',
    ]
);
