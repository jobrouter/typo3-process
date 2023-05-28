<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Dto;

use Brotkrueml\JobRouterProcess\Crypt\Transfer\EncryptedFieldsBitSet;
use Brotkrueml\JobRouterProcess\Domain\Dto\Transfer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TransferTest extends TestCase
{
    private Transfer $subject;

    protected function setUp(): void
    {
        $this->subject = new Transfer(1234567890, 42, 'some-correlation');
    }

    #[Test]
    public function toArrayWithOnlyConstructorArgumentsSet(): void
    {
        $actual = $this->subject->toArray();

        self::assertSame(1234567890, $actual['crdate']);
        self::assertSame(42, $actual['step_uid']);
        self::assertSame('some-correlation', $actual['correlation_id']);
        self::assertSame('', $actual['type']);
        self::assertSame('', $actual['initiator']);
        self::assertSame('', $actual['username']);
        self::assertSame('', $actual['jobfunction']);
        self::assertSame('', $actual['summary']);
        self::assertSame(2, $actual['priority']);
        self::assertSame(1, $actual['pool']);
        self::assertSame('', $actual['processtable']);
        self::assertSame(0, $actual['encrypted_fields']);
    }

    #[Test]
    public function toArrayWithAllOtherPropertiesSet(): void
    {
        $this->subject->setType('some type');
        $this->subject->setInitiator('some initiator');
        $this->subject->setUsername('some username');
        $this->subject->setJobfunction('some jobfunction');
        $this->subject->setSummary('some summary');
        $this->subject->setPriority(1);
        $this->subject->setPool(2);
        $this->subject->setProcesstable('{"some": "processtable"}');
        $this->subject->setEncryptedFields(new EncryptedFieldsBitSet(2));

        $actual = $this->subject->toArray();

        self::assertSame('some type', $actual['type']);
        self::assertSame('some initiator', $actual['initiator']);
        self::assertSame('some username', $actual['username']);
        self::assertSame('some jobfunction', $actual['jobfunction']);
        self::assertSame('some summary', $actual['summary']);
        self::assertSame(1, $actual['priority']);
        self::assertSame(2, $actual['pool']);
        self::assertSame('{"some": "processtable"}', $actual['processtable']);
        self::assertSame(2, $actual['encrypted_fields']);
    }

    #[Test]
    public function toArrayReturnsShortenedInitiatorWhenTooLong(): void
    {
        $this->subject->setInitiator(\str_repeat('abc', 100));

        $actual = $this->subject->toArray();

        self::assertSame(50, \mb_strlen((string)$actual['initiator']));
    }

    #[Test]
    public function toArrayReturnsShortenedUsernameWhenTooLong(): void
    {
        $this->subject->setUsername(\str_repeat('abc', 100));

        $actual = $this->subject->toArray();

        self::assertSame(50, \mb_strlen((string)$actual['username']));
    }

    #[Test]
    public function toArrayReturnsShortenedJobfunctionWhenTooLong(): void
    {
        $this->subject->setJobfunction(\str_repeat('abc', 100));

        $actual = $this->subject->toArray();

        self::assertSame(50, \mb_strlen((string)$actual['jobfunction']));
    }

    #[Test]
    public function toArrayReturnsShortenedSummaryWhenTooLong(): void
    {
        $this->subject->setSummary(\str_repeat('abc', 300));

        $actual = $this->subject->toArray();

        self::assertSame(255, \mb_strlen((string)$actual['summary']));
    }
}
