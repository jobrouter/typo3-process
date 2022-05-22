<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Widgets\Provider;

use Brotkrueml\JobRouterBase\Domain\Model\TransferReportItem;
use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Widgets\Provider\TransferReportDataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

class TransferReportDataProviderTest extends TestCase
{
    /**
     * @var Stub&TransferRepository
     */
    private $transferRepositoryStub;
    private TransferReportDataProvider $subject;

    protected function setUp(): void
    {
        if (! \interface_exists(ListDataProviderInterface::class)) {
            self::markTestSkipped('Dashboard system extension not available');
        }

        $this->transferRepositoryStub = $this->createStub(TransferRepository::class);

        $this->subject = new TransferReportDataProvider($this->transferRepositoryStub);
    }

    /**
     * @test
     */
    public function getItemsReturnsEmptyArrayIfNoErrorsFound(): void
    {
        $this->transferRepositoryStub
            ->method('findErroneousTransfers')
            ->willReturn([]);

        self::assertSame([], $this->subject->getItems());
    }

    /**
     * @test
     */
    public function getItemsReturnsItemsCorrectlyIfErrorsFound(): void
    {
        $transfer1 = new Transfer();
        $transfer1->_setProperty('crdate', 1615052053);
        $transfer1->setStartMessage('some message');
        $transfer1->setCorrelationId('some correlation id');

        $transfer2 = new Transfer();
        $transfer2->_setProperty('crdate', 1615052084);
        $transfer2->setStartMessage('another message');
        $transfer2->setCorrelationId('another correlation id');

        $this->transferRepositoryStub
            ->method('findErroneousTransfers')
            ->willReturn([$transfer1, $transfer2]);

        /** @var TransferReportItem[] $actual */
        $actual = $this->subject->getItems();

        self::assertCount(2, $actual);
        self::assertInstanceOf(TransferReportItem::class, $actual[0]);
        self::assertSame(1615052053, $actual[0]->getCreationDate());
        self::assertSame('some message', $actual[0]->getMessage());
        self::assertSame('some correlation id', $actual[0]->getCorrelationId());
        self::assertInstanceOf(TransferReportItem::class, $actual[1]);
        self::assertSame(1615052084, $actual[1]->getCreationDate());
        self::assertSame('another message', $actual[1]->getMessage());
        self::assertSame('another correlation id', $actual[1]->getCorrelationId());
    }
}
