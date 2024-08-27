<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use JobRouter\AddOn\Typo3Process\Extension;

return [
    'dependencies' => [
        'core',
    ],
    'imports' => [
        '@jobrouter/process/' => 'EXT:' . Extension::KEY . '/Resources/Public/JavaScript/',
    ],
];
