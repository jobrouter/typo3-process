<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Process',
    'description' => 'Connect JobRouter® processes with TYPO3',
    'category' => 'module',
    'author' => 'Chris Müller',
    'author_company' => 'JobRouter GmbH',
    'state' => 'stable',
    'version' => '5.0.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.3.99',
            'form' => '13.4.0-14.3.99',
            'jobrouter_base' => '5.0.0-5.99.99',
            'jobrouter_connector' => '5.0.0-5.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['JobRouter\AddOn\Typo3Process\\' => 'Classes']
    ],
];
