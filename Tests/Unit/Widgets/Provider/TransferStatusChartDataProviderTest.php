<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Widgets\Provider;

use Brotkrueml\JobRouterProcess\Widgets\Provider\TransferStatusChartDataProvider;
use Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterProcess\Extension;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class TransferStatusChartDataProviderTest extends TestCase
{
    /**
     * @var TransferStatusChartDataProvider
     */
    private $subject;

    /**
     * @var TransferRepository|Stub
     */
    private $transferRepositoryStub;

    protected function setUp(): void
    {
        if (!\interface_exists(ChartDataProviderInterface::class)) {
            self::markTestSkipped('Dashboard system extension not available');
        }

        $translationMap = [
            [Extension::LANGUAGE_PATH_DASHBOARD . ':status.successful', 'successful status'],
            [Extension::LANGUAGE_PATH_DASHBOARD . ':status.pending', 'pending status'],
            [Extension::LANGUAGE_PATH_DASHBOARD . ':status.failed', 'failed status'],
        ];

        $languageServiceStub = $this->createStub(LanguageService::class);
        $languageServiceStub
            ->method('sL')
            ->willReturnMap($translationMap);

        $this->transferRepositoryStub = $this->createStub(TransferRepository::class);

        $this->subject = new TransferStatusChartDataProvider(
            $languageServiceStub,
            $this->transferRepositoryStub
        );
    }

    /**
     * @test
     */
    public function getChartDataReturnsDataCorrectlyWhenNoEntriesInTransferTable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByStartSuccess')
            ->willReturn([]);

        $actual = $this->subject->getChartData();

        $expected = [
            'datasets' => [
                [
                    'backgroundColor' => ['#4c7e3a', '#fc3', '#a4276a'],
                    'data' => [0, 0, 0],
                ],
            ],
            'labels' => ['successful status', 'pending status', 'failed status'],
        ];

        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getChartDataReturnsDataCorrectlyWhenOnlySuccessfulEntriesInTransferTable(): void
    {
        $this->transferRepositoryStub
            ->method('countGroupByStartSuccess')
            ->willReturn([
                [
                    'start_success' => 1,
                    'count' => 42,
                ],
            ]);

        $actual = $this->subject->getChartData();

        $expected = [
            'datasets' => [
                [
                    'backgroundColor' => ['#4c7e3a', '#fc3', '#a4276a'],
                    'data' => [42, 0, 0],
                ],
            ],
            'labels' => ['successful status', 'pending status', 'failed status'],
        ];

        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getChartDataReturnsDataCorrectlyWhenOnlyPendingEntriesInTransferTable(): void
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

        $actual = $this->subject->getChartData();

        $expected = [
            'datasets' => [
                [
                    'backgroundColor' => ['#4c7e3a', '#fc3', '#a4276a'],
                    'data' => [0, 13, 0],
                ],
            ],
            'labels' => ['successful status', 'pending status', 'failed status'],
        ];

        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getChartDataReturnsDataCorrectlyWhenOnlyFailedEntriesInTransferTable(): void
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

        $actual = $this->subject->getChartData();

        $expected = [
            'datasets' => [
                [
                    'backgroundColor' => ['#4c7e3a', '#fc3', '#a4276a'],
                    'data' => [0, 0, 22],
                ],
            ],
            'labels' => ['successful status', 'pending status', 'failed status'],
        ];

        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getChartDataReturnsDataCorrectlyWhenAllStatusesAreAvailable(): void
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

        $actual = $this->subject->getChartData();

        $expected = [
            'datasets' => [
                [
                    'backgroundColor' => ['#4c7e3a', '#fc3', '#a4276a'],
                    'data' => [19, 7, 5],
                ],
            ],
            'labels' => ['successful status', 'pending status', 'failed status'],
        ];

        self::assertSame($expected, $actual);
    }
}
