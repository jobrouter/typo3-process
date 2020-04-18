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
use Brotkrueml\JobRouterProcess\Domain\Model\CommonStepParameterTrait;
use PHPUnit\Framework\TestCase;

class DefaultStepParameterTraitTest extends TestCase
{
    /**
     * @var CommonStepParameterInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new class() implements CommonStepParameterInterface {
            use CommonStepParameterTrait;
        };
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
    public function setPriorityAcceptsNumericString(): void
    {
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->subject->setPriority('1');

        self::assertSame(1, $this->subject->getPriority());
    }

    /**
     * @test
     */
    public function setPriorityThrowsExceptionWhenNotANumberGiven(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1581703249);

        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->subject->setPriority('abc');
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
    public function setPoolAcceptsNumericString(): void
    {
        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->subject->setPool('1234');

        self::assertSame(1234, $this->subject->getPool());
    }

    /**
     * @test
     */
    public function setPoolThrowsExceptionWhenNotANumberGiven(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1581703327);

        /** @noinspection PhpStrictTypeCheckingInspection */
        $this->subject->setPool('abc');
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
        self::assertCount(0, $this->subject->getDefaultParameters());
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

        self::assertCount(6, $actual);
        self::assertSame('default initiator', $actual['initiator']);
        self::assertSame('default username', $actual['username']);
        self::assertSame('default jobfunction', $actual['jobfunction']);
        self::assertSame('default summary', $actual['summary']);
        self::assertSame(1, $actual['priority']);
        self::assertSame(42, $actual['pool']);
    }
}
