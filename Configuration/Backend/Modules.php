<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use JobRouter\AddOn\Typo3Process\Controller\ListController;
use JobRouter\AddOn\Typo3Process\Extension;

return [
    // "Data" module
    Extension::MODULE_NAME => [
        'parent' => 'jobrouter',
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/jobrouter/process',
        'labels' => Extension::LANGUAGE_PATH_BACKEND_MODULE,
        'iconIdentifier' => 'jobrouter-module-process',
        'routes' => [
            '_default' => [
                'target' => ListController::class . '::handleRequest',
            ],
        ],
    ],
];
