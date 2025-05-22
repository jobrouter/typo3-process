<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Unit\Widgets\Provider;

use JobRouter\AddOn\Typo3Process\Domain\Repository\TransferRepository;
use JobRouter\AddOn\Typo3Process\Extension;
use JobRouter\AddOn\Typo3Process\Widgets\Provider\TransferStatusDataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Registry;

final class TransferStatusDataProviderTest extends TestCase
{
    private TransferStatusDataProvider $subject;
    private Registry&Stub $registryStub;
    private TransferRepository&Stub $transferRepositoryStub;

    protected function setUp(): void
    {
        $this->registryStub = self::createStub(Registry::class);
        $this->transferRepositoryStub = self::createStub(TransferRepository::class);

        $this->subject = new TransferStatusDataProvider(
            $this->registryStub,
            $this->transferRepositoryStub,
        );
    }

    #[Test]
    public function getStatusReturnsDataCorrectlyWhenNoEntriesInTransferTable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByStartSuccess')
            ->willReturn([]);

        $this->transferRepositoryStub
            ->method('findFirstCreationDate')
            ->willReturn(0);

        $actual = $this->subject->getStatus();

        self::assertSame(0, $actual->getFailedCount());
        self::assertSame(0, $actual->getPendingCount());
        self::assertSame(0, $actual->getSuccessfulCount());
        self::assertSame(0, $actual->getNumberOfDays());
    }

    #[Test]
    public function getStatusReturnsDataCorrectlyWhenOnlySuccessfulEntriesInTransferTable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByStartSuccess')
            ->willReturn([
                [
                    'start_success' => 1,
                    'count' => 42,
                ],
            ]);

        $actual = $this->subject->getStatus();

        self::assertSame(0, $actual->getFailedCount());
        self::assertSame(0, $actual->getPendingCount());
        self::assertSame(42, $actual->getSuccessfulCount());
    }

    #[Test]
    public function getStatusReturnsDataCorrectlyWhenOnlyPendingEntriesInTransferTable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByStartSuccess')
            ->willReturn([
                [
                    'start_success' => 0,
                    'count' => 13,
                ],
            ]);

        $this->transferRepositoryStub
            ->method('countStartFailed')
            ->willReturn(0);

        $actual = $this->subject->getStatus();

        self::assertSame(0, $actual->getFailedCount());
        self::assertSame(13, $actual->getPendingCount());
        self::assertSame(0, $actual->getSuccessfulCount());
    }

    #[Test]
    public function getStatusReturnsDataCorrectlyWhenOnlyFailedEntriesInTransferTable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByStartSuccess')
            ->willReturn([
                [
                    'start_success' => 0,
                    'count' => 22,
                ],
            ]);

        $this->transferRepositoryStub
            ->method('countStartFailed')
            ->willReturn(22);

        $actual = $this->subject->getStatus();

        self::assertSame(22, $actual->getFailedCount());
        self::assertSame(0, $actual->getPendingCount());
        self::assertSame(0, $actual->getSuccessfulCount());
    }

    #[Test]
    public function getStatusReturnsDataCorrectlyWhenAllStatusesAreAvailable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByStartSuccess')
            ->willReturn([
                [
                    'start_success' => 0,
                    'count' => 12,
                ],
                [
                    'start_success' => 1,
                    'count' => 19,
                ],
            ]);

        $this->transferRepositoryStub
            ->method('countStartFailed')
            ->willReturn(5);

        $actual = $this->subject->getStatus();

        self::assertSame(5, $actual->getFailedCount());
        self::assertSame(7, $actual->getPendingCount());
        self::assertSame(19, $actual->getSuccessfulCount());
    }

    #[Test]
    public function getStatusReturns1ForGetNumberOfDaysWhenTheEntryIsFromJustNow(): void
    {
        $this->transferRepositoryStub
            ->method('findFirstCreationDate')
            ->willReturn(\time() - 5);

        $actual = $this->subject->getStatus();

        self::assertSame(1, $actual->getNumberOfDays());
    }

    #[Test]
    public function getStatusReturns2ForGetNumberOfDaysWhenTheEntryIsJustOneDayAndASecondAgo(): void
    {
        $this->transferRepositoryStub
            ->method('findFirstCreationDate')
            ->willReturn(\time() - 86401);

        $actual = $this->subject->getStatus();

        self::assertSame(2, $actual->getNumberOfDays());
    }

    #[Test]
    public function getStatusReturnsNullForLastRunWhenNotAvailable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByStartSuccess')
            ->willReturn([]);

        $actual = $this->subject->getStatus();

        self::assertNull($actual->getLastRun());
    }

    #[Test]
    public function getStatusReturnsDataCorrectlyForLastRun(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByStartSuccess')
            ->willReturn([]);

        $this->registryStub
            ->method('get')
            ->with(Extension::REGISTRY_NAMESPACE, 'startCommand.lastRun')
            ->willReturn([
                'start' => 1598978286,
            ]);

        $expected = new \DateTimeImmutable('@1598978286');
        $actual = $this->subject->getStatus();

        self::assertSame(0, $actual->getLastRun() <=> $expected);
    }
}
