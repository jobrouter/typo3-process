<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Process',
    'description' => 'Connect JobRouter® processes with TYPO3',
    'category' => 'module',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'stable',
    'version' => '3.0.0-dev',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-0.0.0',
            'typo3' => '11.5.4-12.4.99',
            'form' => '11.5.4-12.4.99',
            'jobrouter_base' => '2.0.0-2.99.99',
            'jobrouter_connector' => '2.0.0-2.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\JobRouterProcess\\' => 'Classes']
    ],
];
