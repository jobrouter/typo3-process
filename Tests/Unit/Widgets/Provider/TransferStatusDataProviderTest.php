<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Widgets\Provider;

use Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterProcess\Extension;
use Brotkrueml\JobRouterProcess\Widgets\Provider\TransferStatusDataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Registry;

class TransferStatusDataProviderTest extends TestCase
{
    /**
     * @var TransferStatusDataProvider
     */
    private $subject;

    /**
     * @var Registry|Stub
     */
    private $registryStub;

    /**
     * @var TransferRepository|Stub
     */
    private $transferRepositoryStub;

    protected function setUp(): void
    {
        $this->registryStub = $this->createStub(Registry::class);
        $this->transferRepositoryStub = $this->createStub(TransferRepository::class);

        $this->subject = new TransferStatusDataProvider(
            $this->registryStub,
            $this->transferRepositoryStub
        );
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function getStatusReturns1ForGetNumberOfDaysWhenTheEntryIsFromJustNow(): void
    {
        $this->transferRepositoryStub
            ->method('findFirstCreationDate')
            ->willReturn(\time() - 5);

        $actual = $this->subject->getStatus();

        self::assertSame(1, $actual->getNumberOfDays());
    }

    /**
     * @test
     */
    public function getStatusReturns2ForGetNumberOfDaysWhenTheEntryIsJustOneDayAndASecondAgo()
    {
        $this->transferRepositoryStub
            ->method('findFirstCreationDate')
            ->willReturn(\time() - 86401);

        $actual = $this->subject->getStatus();

        self::assertSame(2, $actual->getNumberOfDays());
    }

    /**
     * @test
     */
    public function getStatusReturnsNullForLastRunWhenNotAvailable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByStartSuccess')
            ->willReturn([]);

        $actual = $this->subject->getStatus();

        self::assertNull($actual->getLastRun());
    }

    /**
     * @test
     */
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
