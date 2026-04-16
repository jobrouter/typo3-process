<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addTypoScriptSetup('
    module.tx_form {
      settings {
        yamlConfigurations {
          1581265491 = EXT:jobrouter_process/Configuration/Yaml/FormSetup.yaml
        }
      }
    }

    plugin.tx_form {
      settings {
        yamlConfigurations {
          1581265491 = EXT:jobrouter_process/Configuration/Yaml/FormSetup.yaml
        }
      }
    }
');
