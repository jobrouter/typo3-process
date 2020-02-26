<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Model;

use Brotkrueml\JobRouterProcess\Domain\Model\CommonStepParameterInterface;
use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use PHPUnit\Framework\TestCase;

class TransferTest extends TestCase
{
    /** @var Transfer */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new Transfer();
    }

    /**
     * @test
     */
    public function getPidReturns0(): void
    {
        self::assertSame(0, $this->subject->getPid());
    }

    /**
     * @test
     */
    public function getAndSetStepUidImplementedCorrectly(): void
    {
        self::assertSame(0, $this->subject->getStepUid());

        $this->subject->setStepUid(42);

        self::assertSame(42, $this->subject->getStepUid());
    }

    /**
     * @test
     */
    public function getAndSetIdentifierImplementedCorrectly(): void
    {
        self::assertSame('', $this->subject->getIdentifier());

        $this->subject->setIdentifier('some identifier');

        self::assertSame('some identifier', $this->subject->getIdentifier());
    }

    /**
     * @test
     */
    public function getAndSetProcesstableWithStringAsArgumentImplementedCorrectly(): void
    {
        self::assertSame('', $this->subject->getProcesstable());

        $this->subject->setProcesstable('some data');

        self::assertSame('some data', $this->subject->getProcesstable());
    }

    /**
     * @test
     */
    public function setProcesstableWithArrayAsArgumentImplementedCorrectly(): void
    {
        $this->subject->setProcesstable(['some' => 'data']);

        self::assertSame('{"some":"data"}', $this->subject->getProcesstable());
    }

    /**
     * @test
     */
    public function setProcessTableWithNotAllowedTypeAsArgumentThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1582744898);
        $this->expectExceptionMessage('Argument "processtable" must be either a string or an array, "object" given');

        $this->subject->setProcesstable(new \stdClass());
    }

    /**
     * @test
     */
    public function isAndSetStartSuccessImplementedCorrectly(): void
    {
        self::assertFalse($this->subject->isStartSuccess());

        $this->subject->setStartSuccess(true);

        self::assertTrue($this->subject->isStartSuccess());
    }

    /**
     * @test
     */
    public function getAndSetStartDateImplementedCorrectly(): void
    {
        self::assertNull($this->subject->getStartDate());

        $date = new \DateTime();
        $this->subject->setStartDate($date);

        self::assertSame($date, $this->subject->getStartDate());
    }

    /**
     * @test
     */
    public function getAndSetStartMessageImplementedCorrectly(): void
    {
        self::assertSame('', $this->subject->getStartMessage());

        $this->subject->setStartMessage('some start message');

        self::assertSame('some start message', $this->subject->getStartMessage());
    }

    /**
     * @test
     */
    public function defaultStepParameterInterfaceIsImplemented(): void
    {
        self::assertInstanceOf(CommonStepParameterInterface::class, $this->subject);
    }
}
