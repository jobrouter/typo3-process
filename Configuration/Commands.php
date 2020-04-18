<?php

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
