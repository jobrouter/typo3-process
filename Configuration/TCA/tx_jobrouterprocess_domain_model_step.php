<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step',
        'label' => 'name',
        'descriptionColumn' => 'description',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'disabled',
        ],
        'rootLevel' => 1,
        'searchFields' => 'handle,name,processname,initiator,username,jobfunction,summary,description',
        'iconfile' => 'EXT:' . \Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/tx_jobrouterprocess_domain_model_step.svg',
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
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.handle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30,
                'eval' => 'alphanum_x,required,trim,unique'
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'required,trim'
            ],
        ],
        'process' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.process',
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
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.step_number',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'max' => 5,
                'eval' => 'int,required',
            ],
        ],
        'initiator' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.initiator',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 50,
                'eval' => 'trim',
            ],
        ],
        'username' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.username',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 50,
                'eval' => 'trim',
            ],
        ],
        'jobfunction' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.jobfunction',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 50,
                'eval' => 'trim',
            ],
        ],
        'summary' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.summary',
            'config' => [
                'type' => 'input',
                'size' => 48,
                'max' => 255,
                'eval' => 'trim',
            ],
        ],
        'priority' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.priority',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        0
                    ],
                    [
                        \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.priority.' . \Brotkrueml\JobRouterProcess\Enumeration\Priority::LOW,
                        \Brotkrueml\JobRouterProcess\Enumeration\Priority::LOW
                    ],
                    [
                        \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.priority.' . \Brotkrueml\JobRouterProcess\Enumeration\Priority::NORMAL,
                        \Brotkrueml\JobRouterProcess\Enumeration\Priority::NORMAL
                    ],
                    [
                        \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.priority.' . \Brotkrueml\JobRouterProcess\Enumeration\Priority::HIGH,
                        \Brotkrueml\JobRouterProcess\Enumeration\Priority::HIGH
                    ],
                ],
            ],
        ],
        'pool' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.pool',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'max' => 5,
                'eval' => 'num',
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.description',
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
                handle, name, process, step_number,
                --div--;' . \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tab.parameters,
                --palette--;;defaultParameters,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                disabled,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                description,
        '
        ],
    ],
    'palettes' => [
        'defaultParameters' => [
            'label' => \Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':palette.default_parameters',
            'showitem' => 'summary, --linebreak--, initiator, username, jobfunction, --linebreak--, priority, pool'
        ],
    ],
];
