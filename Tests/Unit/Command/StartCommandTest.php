<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Command;

use Brotkrueml\JobRouterProcess\Command\StartCommand;
use Brotkrueml\JobRouterProcess\Transfer\Starter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Locking\Exception\LockAcquireException;
use TYPO3\CMS\Core\Locking\LockFactory;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StartCommandTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;

    /** @var LockingStrategyInterface|MockObject */
    private $lockerMock;

    /** @var Starter|MockObject */
    private $starterMock;

    protected function setUp(): void
    {
        $this->lockerMock = $this->createMock(LockingStrategyInterface::class);

        $lockFactoryStub = $this->createStub(LockFactory::class);
        $lockFactoryStub
            ->method('createLocker')
            ->willReturn($this->lockerMock);

        GeneralUtility::setSingletonInstance(LockFactory::class, $lockFactoryStub);

        $this->starterMock = $this->createMock(Starter::class);
        GeneralUtility::addInstance(Starter::class, $this->starterMock);

        $this->commandTester = new CommandTester(new StartCommand());
    }

    /**
     * @test
     */
    public function okIsDisplayedWithNoTransfersAvailable(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->starterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn([0, 0]);

        $this->commandTester->execute([]);

        self::assertSame(StartCommand::EXIT_CODE_OK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 0 transfer(s) started successfully',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function okIsDisplayedWithTransfersAvailableAndNoErrors(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->starterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn([3, 0]);

        $this->commandTester->execute([]);

        self::assertSame(StartCommand::EXIT_CODE_OK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 3 transfer(s) started successfully',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function warningIsDisplayedWithErrorsOccured(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->starterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn([3, 1]);

        $this->commandTester->execute([]);

        self::assertSame(StartCommand::EXIT_CODE_ERRORS_ON_START, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[WARNING] 1 out of 3 transfer(s) had errors on start',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function warningIsDisplayedWhenLockCannotBeAcquired(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willThrowException(new LockAcquireException());

        $this->lockerMock
            ->expects(self::never())
            ->method('release');

        $this->starterMock
            ->expects(self::never())
            ->method('run');

        $this->commandTester->execute([]);

        self::assertSame(StartCommand::EXIT_CODE_CANNOT_ACQUIRE_LOCK, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[WARNING] Could not acquire lock, another process is running',
            $this->commandTester->getDisplay()
        );
    }
}
