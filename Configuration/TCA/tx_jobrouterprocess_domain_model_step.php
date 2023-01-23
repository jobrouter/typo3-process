<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step',
        'label' => 'name',
        'descriptionColumn' => 'description',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'disabled',
        ],
        'rootLevel' => 1,
        'searchFields' => 'handle,name,processname,initiator,username,jobfunction,summary,description',
        'iconfile' => 'EXT:' . Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/tx_jobrouterprocess_domain_model_step.svg',
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

        'handle' => [
            'label' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.handle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30,
                'eval' => 'alphanum_x,required,trim,unique',
            ],
        ],
        'name' => [
            'label' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'required,trim',
            ],
        ],
        'process' => [
            'label' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.process',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_jobrouterprocess_domain_model_process',
                'foreign_table_where' => ' ORDER BY tx_jobrouterprocess_domain_model_process.name',
                'eval' => 'int,required',
            ],
        ],
        'step_number' => [
            'label' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.step_number',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'max' => 5,
                'eval' => 'int,required',
            ],
        ],
        'description' => [
            'label' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_step.description',
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
                name, handle, process, step_number,
                --div--;' . Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tab.parameters,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                disabled,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                description,
        ',
        ],
    ],
];
