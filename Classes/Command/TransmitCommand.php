<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Command;

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterProcess\Transfer\Transmitter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class TransmitCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Start instances stored in the transfer table')
            ->setHelp('This command starts process instances in JobRouter installations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $transmitter = GeneralUtility::makeInstance(Transmitter::class);
        [$total, $errors] = $transmitter->run();

        $outputStyle = new SymfonyStyle($input, $output);
        if ($errors) {
            $outputStyle->warning(
                \sprintf('%d out of %d transfer(s) had errors on transmission', $errors, $total)
            );
            return 1;
        }

        $outputStyle->success(\sprintf('%d transfer(s) transmitted successfully', $total));

        return 0;
    }
}
