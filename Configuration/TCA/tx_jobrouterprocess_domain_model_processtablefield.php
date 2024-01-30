<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => JobRouter\AddOn\Typo3Process\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields',
        'label' => 'description',
        'label_alt' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'type' => 'type',
        'rootLevel' => 1,
        'searchFields' => 'name,description',
        'iconfile' => 'EXT:' . JobRouter\AddOn\Typo3Process\Extension::KEY . '/Resources/Public/Icons/tx_jobrouterprocess_domain_model_processtablefields.svg',
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
            'label' => JobRouter\AddOn\Typo3Process\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields.name',
            'description' => JobRouter\AddOn\Typo3Process\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields.name.description',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max' => 20,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'description' => [
            'label' => JobRouter\AddOn\Typo3Process\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim',
            ],
        ],
        'type' => [
            'label' => JobRouter\AddOn\Typo3Process\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields.type',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Text->value,
                        JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Text->value,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Integer->value,
                        JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Integer->value,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Date->value,
                        JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Date->value,
                    ],
                    [
                        'LLL:EXT:jobrouter_base/Resources/Private/Language/General.xlf:fieldType.' . JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Attachment->value,
                        JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Attachment->value,
                    ],
                ],
                'required' => true,
            ],
        ],
        'field_size' => [
            'label' => JobRouter\AddOn\Typo3Process\Extension::LANGUAGE_PATH_DATABASE . ':tx_jobrouterprocess_domain_model_processtablefields.field_size',
            'config' => [
                'type' => 'number',
                'size' => 5,
                'max' => 5,
                'range' => [
                    'lower' => 0,
                ],
                'default' => 0,
            ],
        ],
    ],
    'types' => [
        (string)JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Text->value => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;nameDescription,
                --palette--;;textType,
            ',
        ],
        (string)JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Integer->value => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;nameDescription,
                type,
            ',
        ],
        (string)JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Date->value => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;nameDescription,
                type,
            ',
        ],
        (string)JobRouter\AddOn\Typo3Base\Enumeration\FieldType::Attachment->value => [
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
