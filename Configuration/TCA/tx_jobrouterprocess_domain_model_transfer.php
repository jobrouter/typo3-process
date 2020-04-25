<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => 'JobRouter Process Transfer',
        'label' => 'identifier',
        'crdate' => 'crdate',
        'rootLevel' => 1,
        'hideTable' => true,
        'iconfile' => 'EXT:' . \Brotkrueml\JobRouterProcess\Extension::KEY . '/Resources/Public/Icons/tx_jobrouterprocess_domain_model_transfer.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'step_uid, identifier, type, start_success, start_date, start_message',
    ],
    'columns' => [
        'step_uid' => [
            'label' => 'Table',
            'config' => [
                'type' => 'input',
            ],
        ],
        'identifier' => [
            'label' => 'Identifier',
            'config' => [
                'type' => 'input',
            ],
        ],
        'type' => [
            'label' => 'Type',
            'config' => [
                'type' => 'input',
            ],
        ],
        'initiator' => [
            'label' => 'Data',
            'config' => [
                'type' => 'input',
            ],
        ],
        'username' => [
            'label' => 'Data',
            'config' => [
                'type' => 'input',
            ],
        ],
        'jobfunction' => [
            'label' => 'Data',
            'config' => [
                'type' => 'input',
            ],
        ],
        'summary' => [
            'label' => 'Data',
            'config' => [
                'type' => 'input',
            ],
        ],
        'priority' => [
            'label' => 'Data',
            'config' => [
                'type' => 'input',
            ],
        ],
        'pool' => [
            'label' => 'Data',
            'config' => [
                'type' => 'input',
            ],
        ],
        'processtable' => [
            'label' => 'Data',
            'config' => [
                'type' => 'input',
            ],
        ],
        'start_success' => [
            'label' => 'Start success',
            'config' => [
                'type' => 'input',
            ],
        ],
        'start_date' => [
            'label' => 'Start date',
            'config' => [
                'type' => 'input',
            ],
        ],
        'start_message' => [
            'label' => 'Start message',
            'config' => [
                'type' => 'input',
            ],
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'step_uid, identifier, data, start_success, start_date, start_message'],
    ],
];
