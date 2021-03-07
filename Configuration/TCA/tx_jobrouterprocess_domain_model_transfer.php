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
    'columns' => [
        'crdate' => [
            'label' => 'Crdate',
            'config' => [
                'type' => 'input',
            ],
        ],
        'step_uid' => [
            'label' => 'Table',
            'config' => [
                'type' => 'input',
            ],
        ],
        'correlation_id' => [
            'label' => 'Correlation id',
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
            'label' => 'Initiator',
            'config' => [
                'type' => 'input',
            ],
        ],
        'username' => [
            'label' => 'Username',
            'config' => [
                'type' => 'input',
            ],
        ],
        'jobfunction' => [
            'label' => 'Job Function',
            'config' => [
                'type' => 'input',
            ],
        ],
        'summary' => [
            'label' => 'Summary',
            'config' => [
                'type' => 'input',
            ],
        ],
        'priority' => [
            'label' => 'Priority',
            'config' => [
                'type' => 'input',
            ],
        ],
        'pool' => [
            'label' => 'Pool',
            'config' => [
                'type' => 'input',
            ],
        ],
        'processtable' => [
            'label' => 'Process table',
            'config' => [
                'type' => 'input',
            ],
        ],
        'encrypted_fields' => [
            'label' => 'Encrypted fields',
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
];
