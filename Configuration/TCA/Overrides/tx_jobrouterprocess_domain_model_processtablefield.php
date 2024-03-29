<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Information\Typo3Version;

defined('TYPO3') || die();

if ((new Typo3Version())->getMajorVersion() === 11) {
    $GLOBALS['TCA']['tx_jobrouterprocess_domain_model_processtablefield']['columns']['field_size']['config'] = array_merge(
        $GLOBALS['TCA']['tx_jobrouterprocess_domain_model_processtablefield']['columns']['field_size']['config'],
        [
            'type' => 'input',
            'eval' => 'int',
        ],
    );
}
