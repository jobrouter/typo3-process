<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'jobrouter-module-process' => [
        'provider' => TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:' . Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/jobrouter-process-module.svg',
    ],
    'jobrouter-process-toolbar' => [
        'provider' => TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:' . Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/jobrouter-process-toolbar.svg',
    ],
    'jobrouter-action-open-designer' => [
        'provider' => TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:' . Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/action-open-designer.svg',
    ],
];
