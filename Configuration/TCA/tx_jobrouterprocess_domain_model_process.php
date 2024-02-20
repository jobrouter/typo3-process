<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use JobRouter\AddOn\Typo3Process\Extension;

return [
    'ctrl' => [
        'title' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_process',
        'label' => 'description',
        'label_alt' => 'name',
        'descriptionColumn' => 'description',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'disabled',
        ],
        'rootLevel' => 1,
        'searchFields' => 'name,description',
        'iconfile' => 'EXT:' . Extension::KEY . '/Resources/Public/Icons/tx_jobrouterprocess_domain_model_process.svg',
        'hideTable' => true,
    ],
    'columns' => [
        'disabled' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],

        'name' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_process.name',
            'description' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_process.name.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'alphanum_x,trim',
                'required' => true,
            ],
        ],
        'connection' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_process.connection',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_jobrouterconnector_domain_model_connection',
                'foreign_table_where' => ' ORDER BY tx_jobrouterconnector_domain_model_connection.name',
                'eval' => 'int',
                'required' => 'true',
            ],
        ],
        'processtablefields' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_process.processtablefields',
            'config' => [
                'type' => 'inline',
                'allowed' => 'tx_jobrouterprocess_domain_model_processtablefield',
                'foreign_table' => 'tx_jobrouterprocess_domain_model_processtablefield',
                'foreign_sortby' => 'sorting',
                'foreign_field' => 'process_uid',
                'minitems' => 0,
                'maxitems' => 100,
                'appearance' => [
                    'collapseAll' => true,
                    'expandSingle' => true,
                    'levelLinksPosition' => 'bottom',
                    'useSortable' => true,
                    'enabledControls' => [
                        'info' => false,
                    ],
                ],
            ],
        ],
        'description' => [
            'label' => Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_process.description',
            'config' => [
                'type' => 'text',
                'rows' => 5,
                'cols' => 30,
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                name, connection, processtablefields,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                disabled,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                description,
            ',
        ],
    ],
];
