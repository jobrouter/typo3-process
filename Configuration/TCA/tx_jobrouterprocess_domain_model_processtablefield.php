<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields',
        'label' => 'description',
        'label_alt' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'type' => 'type',
        'rootLevel' => 1,
        'searchFields' => 'name,description',
        'iconfile' => 'EXT:' . Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/tx_jobrouterprocess_domain_model_processtablefields.svg',
        'hideTable' => true,
    ],
    'columns' => [
        'pid' => [
            'label' => 'pid',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'name' => [
            'label' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields.name',
            'description' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields.name.description',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max' => 20,
                'eval' => 'required,trim',
            ],
        ],
        'description' => [
            'label' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim',
            ],
        ],
        'type' => [
            'label' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields.type',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::TEXT,
                        Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::TEXT,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::INTEGER,
                        Brotkrueml\JobRouterBase\Enumeration\FieldTypeEnumeration::INTEGER,
                    ],
                ],
                'eval' => 'required',
            ],
        ],
        'field_size' => [
            'label' => Brotkrueml\JobRouterProcess\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields.field_size',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'max' => 5,
                'range' => [
                    'lower' => 0,
                ],
                'eval' => 'int',
                'default' => 0,
            ],
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;nameDescription,
                --palette--;;textType,
            ',
        ],
        '2' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;nameDescription,
                type,
            ',
        ],
    ],
    'palettes' => [
        'nameDescription' => [
            'showitem' => 'name, description',
        ],
        'textType' => [
            'showitem' => 'type, field_size',
        ],
    ],
];
