<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_processtablefields',
        'label' => 'description',
        'label_alt' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'rootLevel' => 1,
        'searchFields' => 'name,description',
        'iconfile' => 'EXT:jobrouter_process/Resources/Public/Icons/tx_jobrouterprocess_domain_model_processtablefields.svg',
        'hideTable' => true,
    ],
    'interface' => [
        'showRecordFieldList' => 'name, description, type',
    ],
    'columns' => [
        'pid' => [
            'label' => 'pid',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'passthrough',
            ]
        ],

        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_processtablefields.name',
            'description' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_processtablefields.name.description',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max' => 20,
                'eval' => 'required,trim',
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_processtablefields.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_processtablefields.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', ''],
                    [
                        'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_processtablefields.type.text',
                        \Brotkrueml\JobRouterProcess\Enumeration\ProcessTableFieldTypeEnumeration::TEXT
                    ],
                    [
                        'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_processtablefields.type.integer',
                        \Brotkrueml\JobRouterProcess\Enumeration\ProcessTableFieldTypeEnumeration::INTEGER
                    ],
                ],
                'eval' => 'required',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;nameDescription,
                --palette--;;type,
            '
        ],
    ],
    'palettes' => [
        'nameDescription' => ['showitem' => 'name, description'],
        'type' => ['showitem' => 'type'],
    ],
];
