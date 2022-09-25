<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Process',
    'description' => 'Connect JobRouter® processes with TYPO3',
    'category' => 'module',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'stable',
    'version' => '1.3.0-dev',
    'constraints' => [
        'depends' => [
            'php' => '7.4.0-0.0.0',
            'typo3' => '10.4.11-11.5.99',
            'form' => '10.4.11-11.5.99',
            'jobrouter_base' => '1.4.0-1.99.99',
            'jobrouter_connector' => '1.0.0-1.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\JobRouterProcess\\' => 'Classes']
    ],
];
