<?php
return [
    'ctrl' => [
        'title' => 'Transfer',
        'label' => 'identifier',
        'crdate' => 'crdate',
        'rootLevel' => 1,
        'hideTable' => true,
    ],
    'interface' => [
        'showRecordFieldList' => 'step_uid, identifier, transmit_success, transmit_date, transmit_message',
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
        'transmit_success' => [
            'label' => 'Transmit success',
            'config' => [
                'type' => 'input',
            ],
        ],
        'transmit_date' => [
            'label' => 'Transmit date',
            'config' => [
                'type' => 'input',
            ],
        ],
        'transmit_message' => [
            'label' => 'Transmit message',
            'config' => [
                'type' => 'input',
            ],
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'step_uid, identifier, data, transmit_success, transmit_date, transmit_message'],
    ],
];
