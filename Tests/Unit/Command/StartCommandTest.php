<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Command;

use Brotkrueml\JobRouterProcess\Command\StartCommand;
use Brotkrueml\JobRouterProcess\Transfer\Starter;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StartCommandTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;

    /** @var Starter|Stub */
    private $starterStub;

    protected function setUp(): void
    {
        $this->starterStub = $this->createStub(Starter::class);
        GeneralUtility::addInstance(Starter::class, $this->starterStub);

        $this->commandTester = new CommandTester(new StartCommand());
    }

    /**
     * @test
     */
    public function okIsDisplayedWithNoTransfersAvailable(): void
    {
        $this->starterStub
            ->method('run')
            ->willReturn([0, 0]);

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        self::assertSame('[OK] 0 transfer(s) started successfully', \trim($actual));
    }

    /**
     * @test
     */
    public function okIsDisplayedWithTransfersAvailableAndNoErrors(): void
    {
        $this->starterStub
            ->method('run')
            ->willReturn([3, 0]);

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        self::assertSame('[OK] 3 transfer(s) started successfully', \trim($actual));
    }

    /**
     * @test
     */
    public function warningIsDisplayedWithErrorsOccured(): void
    {
        $this->starterStub
            ->method('run')
            ->willReturn([3, 1]);

        $this->commandTester->execute([]);

        $actual = $this->commandTester->getDisplay();

        self::assertSame('[WARNING] 1 out of 3 transfer(s) had errors on start', \trim($actual));
    }
}
