<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

ExtensionManagementUtility::addTypoScriptSetup('
    module.tx_jobrouterprocess {
      view {
        templateRootPaths {
          0 = EXT:jobrouter_process/Resources/Private/Templates/Backend/
        }

        layoutRootPaths {
          0 = EXT:jobrouter_process/Resources/Private/Layouts/
        }
      }
    }

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
