<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'disabled',
        ],
        'rootLevel' => 1,
        'searchFields' => 'handle,name,processname,initiator,username,jobfunction,summary',
        'iconfile' => 'EXT:jobrouter_process/Resources/Public/Icons/tx_jobrouterprocess_domain_model_step.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'handle, name, processname, step, initiator, username, jobfunction, summary, priority, pool',
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

        'handle' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.handle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30,
                'eval' => 'alphanum_x,required,trim,unique'
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'required,trim'
            ],
        ],
        'process' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.process',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_jobrouterprocess_domain_model_process',
                'foreign_table_where' => ' ORDER BY tx_jobrouterprocess_domain_model_process.name',
                'eval' => 'int,required',
            ],
        ],
        'step_number' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.step_number',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'max' => 5,
                'eval' => 'int,required',
            ],
        ],
        'initiator' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.initiator',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 50,
                'eval' => 'trim',
            ],
        ],
        'username' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.username',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 50,
                'eval' => 'trim',
            ],
        ],
        'jobfunction' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.jobfunction',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 50,
                'eval' => 'trim',
            ],
        ],
        'summary' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.summary',
            'config' => [
                'type' => 'input',
                'size' => 48,
                'max' => 255,
                'eval' => 'trim',
            ],
        ],
        'priority' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.priority',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        0
                    ],
                    [
                        'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.priority.1',
                        1
                    ],
                    [
                        'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.priority.2',
                        2
                    ],
                    [
                        'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.priority.3',
                        3
                    ],
                ],
            ],
        ],
        'pool' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tx_jobrouterprocess_domain_model_step.pool',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'max' => 5,
                'eval' => 'num',
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
            handle, name, process, step_number,
            --div--;LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:tab.parameters,
            --palette--;;defaultParameters,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            disabled,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,
        '
        ],
    ],
    'palettes' => [
        'defaultParameters' => [
            'label' => 'LLL:EXT:jobrouter_process/Resources/Private/Language/Database.xlf:palette.default_parameters',
            'showitem' => 'summary, --linebreak--, initiator, username, jobfunction, --linebreak--, priority, pool'
        ],
    ],
];
