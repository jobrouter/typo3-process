<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Process',
    'description' => 'Connect JobRouter processes with TYPO3',
    'category' => 'plugin',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'alpha',
    'version' => '0.1.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
            'form' => '9.5.0-9.5.99',
            'jobrouter_connector' => '0.8.0-0.8.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\JobRouterProcess\\' => 'Classes']
    ],
];
