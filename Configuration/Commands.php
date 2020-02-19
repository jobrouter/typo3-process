<?php
return [
    'jobrouter:process:start' => [
        'class' => \Brotkrueml\JobRouterProcess\Command\StartCommand::class,
        'schedulable' => true,
    ],
    'jobrouter:process:deleteoldtransfers' => [
        'class' => \Brotkrueml\JobRouterProcess\Command\DeleteOldTransfersCommand::class,
        'schedulable' => true,
    ],
];
