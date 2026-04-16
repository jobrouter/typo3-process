<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use JobRouter\AddOn\Typo3Process\Extension;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgSpriteIconProvider;
use TYPO3\CMS\Core\Information\Typo3Version;

$icons = [
    'jobrouter-module-process' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:' . Extension::KEY . '/Resources/Public/Icons/jobrouter-process-module.svg',
    ],
    'jobrouter-process-toolbar' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:' . Extension::KEY . '/Resources/Public/Icons/jobrouter-process-toolbar.svg',
    ],
    'jobrouter-action-open-designer' => [
        'provider' => SvgSpriteIconProvider::class,
        'sprite' => 'EXT:' . Extension::KEY . '/Resources/Public/Icons/action-open-designer.svg#jobrouter-action-open-designer',
    ],
];

// @todo Remove, once compatibility with TYPO3 v13 is removed
if ((new Typo3Version())->getMajorVersion() < 14) {
    $icons['jobrouter-module-process']['source'] = 'EXT:' . Extension::KEY . '/Resources/Public/Icons/jobrouter-process-module-v13.svg';
}

return $icons;
