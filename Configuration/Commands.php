<?php
return [
    'jobrouter:process:transmit' => [
        'class' => \Brotkrueml\JobRouterProcess\Command\TransmitCommand::class,
        'schedulable' => true,
    ],
];
