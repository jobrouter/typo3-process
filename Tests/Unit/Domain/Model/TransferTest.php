<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Model;

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
    public function getAndSetCorrelationIdImplementedCorrectly(): void
    {
        self::assertSame('', $this->subject->getCorrelationId());

        $this->subject->setCorrelationId('some correlation id');

        self::assertSame('some correlation id', $this->subject->getCorrelationId());
    }

    /**
     * @test
     */
    public function getAndSetTypeImplementedCorrectly(): void
    {
        self::assertSame('', $this->subject->getType());

        $this->subject->setType('some type');

        self::assertSame('some type', $this->subject->getType());
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
    public function getAndSetInitiator(): void
    {
        self::assertSame('', $this->subject->getInitiator());

        $this->subject->setInitiator('some initiator');

        self::assertSame('some initiator', $this->subject->getInitiator());
    }

    /**
     * @test
     */
    public function setInitiatorCutsValueToMaxAllowedCharLength(): void
    {
        $this->subject->setInitiator('12345678901234567890123456789012345678901234567890shouldbecut');

        self::assertSame('12345678901234567890123456789012345678901234567890', $this->subject->getInitiator());
    }

    /**
     * @test
     */
    public function setInitiatorCutsValueToMaxAllowedCharLengthRecognisingMultibyte(): void
    {
        $this->subject->setInitiator('هذا نص طويل للتحقق من الحد الأقصى لطول البادئ. نظرًا لأن الطول غير كافٍ بعد ، فهناك جملة أخرى.');

        self::assertSame('هذا نص طويل للتحقق من الحد الأقصى لطول البادئ. نظر', $this->subject->getInitiator());
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
    public function setUsernameCutsValueToMaxAllowedCharLength(): void
    {
        $this->subject->setUsername('12345678901234567890123456789012345678901234567890shouldbecut');

        self::assertSame('12345678901234567890123456789012345678901234567890', $this->subject->getUsername());
    }

    /**
     * @test
     */
    public function setUsernameCutsValueToMaxAllowedCharLengthRecognisingMultibyte(): void
    {
        $this->subject->setUsername('هذا نص طويل للتحقق من الحد الأقصى لطول البادئ. نظرًا لأن الطول غير كافٍ بعد ، فهناك جملة أخرى.');

        self::assertSame('هذا نص طويل للتحقق من الحد الأقصى لطول البادئ. نظر', $this->subject->getUsername());
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
    public function setJobfunctionCutsValueToMaxAllowedCharLength(): void
    {
        $this->subject->setJobfunction('12345678901234567890123456789012345678901234567890shouldbecut');

        self::assertSame('12345678901234567890123456789012345678901234567890', $this->subject->getJobfunction());
    }

    /**
     * @test
     */
    public function setJobfunctionCutsValueToMaxAllowedCharLengthRecognisingMultibyte(): void
    {
        $this->subject->setJobfunction('هذا نص طويل للتحقق من الحد الأقصى لطول البادئ. نظرًا لأن الطول غير كافٍ بعد ، فهناك جملة أخرى.');

        self::assertSame('هذا نص طويل للتحقق من الحد الأقصى لطول البادئ. نظر', $this->subject->getJobfunction());
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
    public function setSummaryCutsValueToMaxAllowedCharLength(): void
    {
        $this->subject->setSummary('123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345shouldbecut');

        self::assertSame(
            '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
            $this->subject->getSummary()
        );
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
    public function getAndSetEncryptedFields(): void
    {
        self::assertSame(0, $this->subject->getEncryptedFields());

        $this->subject->setEncryptedFields(3);

        self::assertSame(3, $this->subject->getEncryptedFields());
    }
}
