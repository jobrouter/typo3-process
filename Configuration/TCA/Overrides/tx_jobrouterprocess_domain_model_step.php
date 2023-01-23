<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

defined('TYPO3') || die();

if ((new TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion() === 11) {
    $GLOBALS['TCA']['tx_jobrouterprocess_domain_model_step']['columns']['step_number']['config'] = array_merge(
        $GLOBALS['TCA']['tx_jobrouterprocess_domain_model_step']['columns']['step_number']['config'],
        [
            'type' => 'input',
            'eval' => 'int',
        ],
    );
}
