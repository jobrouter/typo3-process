<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Model;

use Brotkrueml\JobRouterProcess\Domain\Model\Step;
use Brotkrueml\JobRouterProcess\Domain\Model\Process;
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
    public function getAndSetInitiator(): void
    {
        self::assertSame('', $this->subject->getInitiator());

        $this->subject->setInitiator('some initiator');

        self::assertSame('some initiator', $this->subject->getInitiator());
    }

    /**
     * @test
     */
    public function getAndSetUsername(): void
    {
        self::assertSame('', $this->subject->getUsername());

        $this->subject->setUsername('some username');

        self::assertSame('some username', $this->subject->getUsername());
    }

    /**
     * @test
     */
    public function getAndSetJobfunction(): void
    {
        self::assertSame('', $this->subject->getJobfunction());

        $this->subject->setJobfunction('some jobfunction');

        self::assertSame('some jobfunction', $this->subject->getJobfunction());
    }

    /**
     * @test
     */
    public function getAndSetSummary(): void
    {
        self::assertSame('', $this->subject->getSummary());

        $this->subject->setSummary('some summary');

        self::assertSame('some summary', $this->subject->getSummary());
    }

    /**
     * @test
     */
    public function getAndSetPriority(): void
    {
        self::assertSame(2, $this->subject->getPriority());

        $this->subject->setPriority(3);

        self::assertSame(3, $this->subject->getPriority());
    }

    /**
     * @test
     */
    public function setPriorityThrowsExceptionWhenANumberTooLowIsSet(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1581282986);

        $this->subject->setPriority(0);
    }

    /**
     * @test
     */
    public function setPriorityThrowsExceptionWhenANumberTooHighIsSet(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1581282986);

        $this->subject->setPriority(4);
    }

    /**
     * @test
     */
    public function getAndSetPool(): void
    {
        self::assertSame(1, $this->subject->getPool());

        $this->subject->setPool(3);

        self::assertSame(3, $this->subject->getPool());
    }

    /**
     * @test
     */
    public function setPoolThrowsExceptionWhenNotAPositiveIntegerIsGiven(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1581283150);

        $this->subject->setPool(0);
    }

    /**
     * @test
     */
    public function getDefaultParametersReturnsEmptyArrayWhenNoDefaultParameterIsDefined(): void
    {
        $this->assertCount(0, $this->subject->getDefaultParameters());
    }

    /**
     * @test
     */
    public function getDefaultParametersReturnsAllDefinedDefaultParametersCorrectly(): void
    {
        $this->subject->setInitiator('default initiator');
        $this->subject->setUsername('default username');
        $this->subject->setJobfunction('default jobfunction');
        $this->subject->setSummary('default summary');
        $this->subject->setPriority(1);
        $this->subject->setPool(42);

        $actual = $this->subject->getDefaultParameters();

        $this->assertCount(6, $actual);
        $this->assertSame('default initiator', $actual['initiator']);
        $this->assertSame('default username', $actual['username']);
        $this->assertSame('default jobfunction', $actual['jobfunction']);
        $this->assertSame('default summary', $actual['summary']);
        $this->assertSame(1, $actual['priority']);
        $this->assertSame(42, $actual['pool']);
    }
}
