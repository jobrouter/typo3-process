<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Process',
    'description' => 'Connect JobRouter® processes with TYPO3',
    'category' => 'module',
    'author' => 'Chris Müller',
    'author_company' => 'JobRouter AG',
    'state' => 'stable',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-0.0.0',
            'typo3' => '11.5.4-12.4.99',
            'form' => '11.5.4-12.4.99',
            'jobrouter_base' => '3.0.0-3.99.99',
            'jobrouter_connector' => '3.0.0-3.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['JobRouter\AddOn\Typo3Process\\' => 'Classes']
    ],
];
