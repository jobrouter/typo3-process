<?php
defined('TYPO3') || die();

(function () {
    $configuration = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get(Brotkrueml\JobRouterProcess\Extension::KEY);

    $writerConfiguration = [];
    if ($configuration['logIntoFile']) {
        $writerConfiguration[TYPO3\CMS\Core\Log\Writer\FileWriter::class] = ['logFileInfix' => Brotkrueml\JobRouterProcess\Extension::KEY];
    }
    if ($configuration['logIntoTable']) {
        $writerConfiguration[TYPO3\CMS\Core\Log\Writer\DatabaseWriter::class] = ['logTable' => 'tx_jobrouterbase_log'];
    }

    if (!empty($writerConfiguration)) {
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Brotkrueml']['JobRouterProcess']['writerConfiguration'][$configuration['logLevel']]
            = $writerConfiguration;
    }
})();
