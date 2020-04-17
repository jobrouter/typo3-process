<?php
return [
    'jobrouter:process:start' => [
        'class' => \Brotkrueml\JobRouterProcess\Command\StartCommand::class,
        'schedulable' => true,
    ],
    'jobrouter:process:cleanuptransfers' => [
        'class' => \Brotkrueml\JobRouterProcess\Command\CleanUpTransfersCommand::class,
        'schedulable' => true,
    ],
];
