<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_process',
        'label' => 'description',
        'label_alt' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'disabled',
        ],
        'rootLevel' => 1,
        'searchFields' => 'name',
        'iconfile' => 'EXT:jobrouter_process/Resources/Public/Icons/tx_jobrouterprocess_domain_model_process.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'name, description, connection, processtablefields',
    ],
    'columns' => [
        'disabled' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ]
        ],

        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_process.name',
            'description' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_process.name.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'alphanum_x,required,trim'
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_process.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'connection' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_process.connection',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_jobrouterconnector_domain_model_connection',
                'foreign_table_where' => ' ORDER BY tx_jobrouterconnector_domain_model_connection.name',
                'eval' => 'int,required',
            ],
        ],
        'processtablefields' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_process.processtablefields',
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
    ],
    'types' => [
        '1' => [
            'showitem' => '
            name, description, connection, processtablefields,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            disabled
        '
        ],
    ],
];
