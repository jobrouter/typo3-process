<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Information\Typo3Version;

// @todo Remove, once compatibility with TYPO3 v13 is removed
if ((new Typo3Version())->getMajorVersion() < 14) {
    $GLOBALS['TCA']['tx_jobrouterprocess_domain_model_step']['ctrl']['searchFields'] = 'handle,name';
}
