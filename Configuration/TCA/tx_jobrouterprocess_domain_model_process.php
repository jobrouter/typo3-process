<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_process',
        'label' => 'name',
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
        'showRecordFieldList' => 'name, connection',
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
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'required,trim'
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
            name, connection, processtablefields,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            disabled
        '
        ],
    ],
];
