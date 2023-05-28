<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Entity;

use Brotkrueml\JobRouterProcess\Domain\Entity\Transfer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TransferTest extends TestCase
{
    #[Test]
    public function fromArray(): void
    {
        $actual = Transfer::fromArray([
            'uid' => '21',
            'crdate' => '1234567890',
            'step_uid' => '42',
            'correlation_id' => 'some-correlation-id',
            'type' => 'some type',
            'initiator' => 'some initiator',
            'username' => 'some username',
            'jobfunction' => 'some jobfunction',
            'summary' => 'some summary',
            'priority' => '2',
            'pool' => '3',
            'processtable' => '{"some": "processtable"}',
            'encrypted_fields' => '3',
            'start_success' => '1',
            'start_date' => '3456789012',
            'start_message' => 'some start message',
        ]);

        self::assertSame(21, $actual->uid);
        self::assertSame(1234567890, $actual->crdate);
        self::assertSame(42, $actual->stepUid);
        self::assertSame('some-correlation-id', $actual->correlationId);
        self::assertSame('some type', $actual->type);
        self::assertSame('some initiator', $actual->initiator);
        self::assertSame('some username', $actual->username);
        self::assertSame('some jobfunction', $actual->jobfunction);
        self::assertSame('some summary', $actual->summary);
        self::assertSame(2, $actual->priority);
        self::assertSame(3, $actual->pool);
        self::assertSame('{"some": "processtable"}', $actual->processtable);
        self::assertSame(3, $actual->encryptedFields->__toInt());
        self::assertTrue($actual->startSuccess);
        self::assertSame(3456789012, $actual->startDate->getTimestamp());
        self::assertSame('some start message', $actual->startMessage);
    }
}
