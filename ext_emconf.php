<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Process',
    'description' => 'Connect JobRouter® processes with TYPO3',
    'category' => 'module',
    'author' => 'Chris Müller',
    'author_company' => 'JobRouter AG',
    'state' => 'stable',
    'version' => '4.0.1',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-0.0.0',
            'typo3' => '12.4.9-13.4.99',
            'form' => '12.4.9-13.4.99',
            'jobrouter_base' => '4.0.0-4.99.99',
            'jobrouter_connector' => '4.0.0-4.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['JobRouter\AddOn\Typo3Process\\' => 'Classes']
    ],
];
