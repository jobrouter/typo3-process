<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Model;

use Brotkrueml\JobRouterProcess\Domain\Model\CommonStepParameterInterface;
use Brotkrueml\JobRouterProcess\Domain\Model\Process;
use Brotkrueml\JobRouterProcess\Domain\Model\Step;
use PHPUnit\Framework\TestCase;

class StepTest extends TestCase
{
    /** @var Step */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new Step();
    }

    /**
     * @test
     */
    public function getAndSetHandle(): void
    {
        self::assertSame('', $this->subject->getHandle());

        $this->subject->setHandle('some handle');

        self::assertSame('some handle', $this->subject->getHandle());
    }

    /**
     * @test
     */
    public function getAndSetName(): void
    {
        self::assertSame('', $this->subject->getName());

        $this->subject->setName('some name');

        self::assertSame('some name', $this->subject->getName());
    }

    /**
     * @test
     */
    public function getAndSetProcess(): void
    {
        self::assertNull($this->subject->getProcess());

        $process = new Process();
        $this->subject->setProcess($process);

        self::assertSame($process, $this->subject->getProcess());
    }

    /**
     * @test
     */
    public function getAndSetStepNumber(): void
    {
        self::assertSame(0, $this->subject->getStepNumber());

        $this->subject->setStepNumber(42);

        self::assertSame(42, $this->subject->getStepNumber());
    }

    /**
     * @test
     */
    public function setStepNumberThrowsExceptionWhen0IsSet(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1581282590);

        $this->subject->setStepNumber(0);
    }

    /**
     * @test
     */
    public function setStepNumberThrowsExceptionWhenANegativeNumberIsSet(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1581282590);

        $this->subject->setStepNumber(-42);
    }

    /**
     * @test
     */
    public function defaultStepParameterInterfaceIsImplemented(): void
    {
        self::assertInstanceOf(CommonStepParameterInterface::class, $this->subject);
    }
}
