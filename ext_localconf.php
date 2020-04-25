<?php
defined('TYPO3_MODE') || die('Access denied.');

(function () {
    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get(Brotkrueml\JobRouterProcess\Extension::KEY);

    $writerConfiguration = [];
    if ($configuration['logIntoFile']) {
        $writerConfiguration[\TYPO3\CMS\Core\Log\Writer\FileWriter::class] = ['logFileInfix' => Brotkrueml\JobRouterProcess\Extension::KEY];
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
    $hookNamespace = 'ext/' . Brotkrueml\JobRouterProcess\Extension::KEY;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$hookNamespace]['variableResolvers'][] =
        \Brotkrueml\JobRouterProcess\Domain\VariableResolver\TransferIdentifierVariableResolver::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$hookNamespace]['variableResolvers'][] =
        \Brotkrueml\JobRouterProcess\Domain\VariableResolver\LanguageVariableResolver::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$hookNamespace]['variableResolvers'][] =
        \Brotkrueml\JobRouterProcess\Domain\VariableResolver\LocalisedLabelVariableResolver::class;
})();
