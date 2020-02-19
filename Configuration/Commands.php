<?php
return [
    'jobrouter:process:start' => [
        'class' => \Brotkrueml\JobRouterProcess\Command\StartCommand::class,
        'schedulable' => true,
    ],
];
