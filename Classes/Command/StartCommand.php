<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Command;

use Brotkrueml\JobRouterProcess\Extension;
use Brotkrueml\JobRouterProcess\Transfer\Starter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Locking\Exception as LockException;
use TYPO3\CMS\Core\Locking\LockFactory;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
final class StartCommand extends Command
{
    public const EXIT_CODE_OK = 0;
    public const EXIT_CODE_ERRORS_ON_START = 1;
    public const EXIT_CODE_CANNOT_ACQUIRE_LOCK = 2;

    /** @var int */
    private $startTime;

    /** @var LockingStrategyInterface */
    private $locker;

    protected function configure(): void
    {
        $this
            ->setDescription('Start instances stored in the transfer table')
            ->setHelp('This command starts process instances in JobRouter installations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->startTime = time();
        $outputStyle = new SymfonyStyle($input, $output);
        $lockFactory = GeneralUtility::makeInstance(LockFactory::class);

        try {
            $this->locker = $lockFactory->createLocker(self::class, LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE);
            $this->locker->acquire(LockingStrategyInterface::LOCK_CAPABILITY_EXCLUSIVE | LockingStrategyInterface::LOCK_CAPABILITY_NOBLOCK);

            [$exitCode, $messageType, $message] = $this->start();
            $this->locker->release();
            $outputStyle->{$messageType}($message);

            $this->recordLastRun($exitCode);

            return $exitCode;
        } catch (LockException $e) {
            $outputStyle->warning('Could not acquire lock, another process is running');

            return self::EXIT_CODE_CANNOT_ACQUIRE_LOCK;
        }
    }

    private function start(): array
    {
        $starter = GeneralUtility::makeInstance(Starter::class);
        [$total, $errors] = $starter->run();

        if ($errors) {
            return [
                self::EXIT_CODE_ERRORS_ON_START,
                'warning',
                \sprintf('%d out of %d incident(s) had errors on start', $errors, $total),
            ];
        }

        return [
            self::EXIT_CODE_OK,
            'success',
            \sprintf('%d incident(s) started successfully', $total),
        ];
    }

    private function recordLastRun(int $exitCode): void
    {
        $registry = GeneralUtility::makeInstance(Registry::class);
        $runInformation = [
            'start' => $this->startTime,
            'end' => time(),
            'exitCode' => $exitCode,
        ];
        $registry->set(Extension::REGISTRY_NAMESPACE, 'startCommand.lastRun', $runInformation);
    }
}
