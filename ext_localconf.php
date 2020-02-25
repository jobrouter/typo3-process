<?php
defined('TYPO3_MODE') || die('Access denied.');

(function ($extensionKey = 'jobrouter_process') {
    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get($extensionKey);

    $writerConfiguration = [];
    if ($configuration['logIntoFile']) {
        $writerConfiguration[\TYPO3\CMS\Core\Log\Writer\FileWriter::class] = ['logFileInfix' => $extensionKey];
    }
    if ($configuration['logIntoTable']) {
        $writerConfiguration[\TYPO3\CMS\Core\Log\Writer\DatabaseWriter::class] = ['logTable' => 'tx_jobrouterconnector_log'];
    }

    if (!empty($writerConfiguration)) {
        $logLevel = (int)$configuration['logLevel'];
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Brotkrueml']['JobRouterProcess']['writerConfiguration'][$logLevel]
            = $writerConfiguration;
    }

    // @todo Use PSR-14 events in TYPO3 v10
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/' . $extensionKey]['variableResolvers'][] =
        \Brotkrueml\JobRouterProcess\Domain\VariableResolver\TransferIdentifierVariableResolver::class;
})();
