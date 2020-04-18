<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'JobRouter Process',
    'description' => 'Connect JobRouter processes with TYPO3',
    'category' => 'module',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'beta',
    'version' => '0.3.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.4.99',
            'form' => '9.5.0-10.4.99',
            'jobrouter_connector' => '0.9.0-0.10.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\JobRouterProcess\\' => 'Classes']
    ],
];
