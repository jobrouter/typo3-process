<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Unit\Widgets\Provider;

use JobRouter\AddOn\Typo3Base\Domain\Dto\TransferReportItem;
use JobRouter\AddOn\Typo3Process\Domain\Entity\Transfer;
use JobRouter\AddOn\Typo3Process\Domain\Repository\TransferRepository;
use JobRouter\AddOn\Typo3Process\Widgets\Provider\TransferReportDataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

final class TransferReportDataProviderTest extends TestCase
{
    private TransferRepository&Stub $transferRepositoryStub;
    private TransferReportDataProvider $subject;

    protected function setUp(): void
    {
        if (! \interface_exists(ListDataProviderInterface::class)) {
            self::markTestSkipped('Dashboard system extension not available');
        }

        $this->transferRepositoryStub = $this->createStub(TransferRepository::class);

        $this->subject = new TransferReportDataProvider($this->transferRepositoryStub);
    }

    #[Test]
    public function getItemsReturnsEmptyArrayIfNoErrorsFound(): void
    {
        $this->transferRepositoryStub
            ->method('findErroneous')
            ->willReturn([]);

        self::assertSame([], $this->subject->getItems());
    }

    #[Test]
    public function getItemsReturnsItemsCorrectlyIfErrorsFound(): void
    {
        $transfer1 = Transfer::fromArray([
            'uid' => 1,
            'crdate' => 1615052053,
            'step_uid' => 42,
            'correlation_id' => 'some correlation id',
            'type' => '',
            'initiator' => '',
            'username' => '',
            'jobfunction' => '',
            'summary' => '',
            'priority' => 2,
            'pool' => 1,
            'processtable' => '',
            'encrypted_fields' => 0,
            'start_success' => 0,
            'start_date' => 123,
            'start_message' => 'some message',
        ]);

        $transfer2 = Transfer::fromArray([
            'uid' => 2,
            'crdate' => 1615052084,
            'step_uid' => 42,
            'correlation_id' => 'another correlation id',
            'type' => '',
            'initiator' => '',
            'username' => '',
            'jobfunction' => '',
            'summary' => '',
            'priority' => 2,
            'pool' => 1,
            'processtable' => '',
            'encrypted_fields' => 0,
            'start_success' => 0,
            'start_date' => 123,
            'start_message' => 'another message',
        ]);

        $this->transferRepositoryStub
            ->method('findErroneous')
            ->willReturn([$transfer1, $transfer2]);

        /** @var TransferReportItem[] $actual */
        $actual = $this->subject->getItems();

        self::assertCount(2, $actual);
        self::assertInstanceOf(TransferReportItem::class, $actual[0]);
        self::assertSame(1615052053, $actual[0]->creationDate);
        self::assertSame('some message', $actual[0]->message);
        self::assertSame('some correlation id', $actual[0]->correlationId);
        self::assertInstanceOf(TransferReportItem::class, $actual[1]);
        self::assertSame(1615052084, $actual[1]->creationDate);
        self::assertSame('another message', $actual[1]->message);
        self::assertSame('another correlation id', $actual[1]->correlationId);
    }
}
