<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Widgets\Provider;

use Brotkrueml\JobRouterBase\Extension as BaseExtension;
use Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterProcess\Extension;
use Brotkrueml\JobRouterProcess\Widgets\Provider\TransfersPerDayDataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class TransfersPerDayDataProviderTest extends TestCase
{
    /**
     * @var Stub|TransferRepository
     */
    private $transferRepositoryStub;

    /**
     * @var TransfersPerDayDataProvider
     */
    private $subject;

    protected function setUp(): void
    {
        if (!\interface_exists(ChartDataProviderInterface::class)) {
            self::markTestSkipped('Dashboard system extension not available');
        }

        $translationMap = [
            [BaseExtension::LANGUAGE_PATH_GENERAL . ':dateFormat', 'd.m.Y'],
            [Extension::LANGUAGE_PATH_DASHBOARD . ':numberOfStarts', 'starts count'],
        ];

        $languageServiceStub = $this->createStub(LanguageService::class);
        $languageServiceStub
            ->method('sL')
            ->willReturnMap($translationMap);

        $this->transferRepositoryStub = $this->createStub(TransferRepository::class);

        $this->subject = new TransfersPerDayDataProvider(
            $languageServiceStub,
            $this->transferRepositoryStub
        );
        $this->subject->setNumberOfDays(5);
    }

    /**
     * @test
     * @dataProvider dataProviderForGetChartData
     * @param array $countByDay
     * @param array $expected
     */
    public function getChartData(array $countByDay, array $expected): void
    {
        $this->transferRepositoryStub
            ->method('countByDay')
            ->with(5)
            ->willReturn($countByDay);

        self::assertSame($expected, $this->subject->getChartData());
    }

    public function dataProviderForGetChartData(): \Generator
    {
        $timestamps = $this->getTimestamps();
        $days = $this->getDays($timestamps);

        yield 'Returns all counts with 0 when no transfers available' => [
            [],
            [
                'labels' => $days,
                'datasets' => [
                    [
                        'label' => 'starts count',
                        'backgroundColor' => '#fc3',
                        'data' => [0, 0, 0, 0, 0],
                    ],
                ],
            ],
        ];

        yield 'Returns all counts correctly when at all days transfers available' => [
            [
                [
                    'day' => $timestamps[0],
                    'count' => 1,
                ],
                [
                    'day' => $timestamps[1],
                    'count' => 2,
                ],
                [
                    'day' => $timestamps[2],
                    'count' => 3,
                ],
                [
                    'day' => $timestamps[3],
                    'count' => 4,
                ],
                [
                    'day' => $timestamps[4],
                    'count' => 5,
                ],
            ],
            [
                'labels' => $days,
                'datasets' => [
                    [
                        'label' => 'starts count',
                        'backgroundColor' => '#fc3',
                        'data' => [1, 2, 3, 4, 5],
                    ],
                ],
            ],
        ];

        yield 'Returns counts correctly when only one day transfers available' => [
            [
                [
                    'day' => $timestamps[2],
                    'count' => 42,
                ],
            ],
            [
                'labels' => $days,
                'datasets' => [
                    [
                        'label' => 'starts count',
                        'backgroundColor' => '#fc3',
                        'data' => [0, 0, 42, 0, 0],
                    ],
                ],
            ],
        ];
    }

    private function getTimestamps(): array
    {
        $today = new \DateTime('now', new \DateTimeZone('UTC'));
        $today->setTime(0, 0);

        $timestamps = [];
        for ($ts = 4; $ts >= 0; $ts--) {
            $date = clone $today;
            $date->sub(new \DateInterval('P' . $ts . 'D'));
            $timestamps[] = (int)$date->format('U');
        }

        return $timestamps;
    }

    private function getDays($timestamps): array
    {
        $days = [];
        foreach ($timestamps as $timestamp) {
            $days[] = \date('d.m.Y', $timestamp);
        }

        return $days;
    }
}
