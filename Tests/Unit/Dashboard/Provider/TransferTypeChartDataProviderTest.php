<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Dashboard\Provider;

use Brotkrueml\JobRouterProcess\Dashboard\Provider\TransferTypeChartDataProvider;
use Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder\TransferRepository;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class TransferTypeChartDataProviderTest extends TestCase
{
    /**
     * @var Stub|TransferRepository
     */
    private $transferRepositoryStub;

    /**
     * @var TransferTypeChartDataProvider
     */
    private $subject;

    protected function setUp(): void
    {
        if (!\interface_exists(ChartDataProviderInterface::class)) {
            self::markTestSkipped('Dashboard system extension not available');
        }

        $languageServiceStub = $this->createStub(LanguageService::class);
        $languageServiceStub
            ->method('sL')
            ->willReturn('unknown');

        $this->transferRepositoryStub = $this->createStub(TransferRepository::class);

        $this->subject = new TransferTypeChartDataProvider(
            $languageServiceStub,
            $this->transferRepositoryStub
        );
        $this->subject->setNumberOfDays(13);
    }

    /**
     * @test
     * @dataProvider dataProviderForGetChartData
     * @param array $countTypesResult
     * @param array $expected
     */
    public function getChartData(array $countTypesResult, array $expected): void
    {
        $this->transferRepositoryStub
            ->method('countTypes')
            ->with(13)
            ->willReturn($countTypesResult);

        self::assertSame($expected, $this->subject->getChartData());
    }

    public function dataProviderForGetChartData(): \Generator
    {
        yield 'Returns empty arrays when no transfers available' => [
            [],
            [
                'datasets' => [
                    [
                        'backgroundColor' => [],
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ]
        ];

        yield 'Returns unknown type when type is empty available' => [
            [
                [
                    'type' => '',
                    'count' => 3,
                ],
            ],
            [
                'datasets' => [
                    [
                        'backgroundColor' => ['#fc3'],
                        'data' => [3],
                    ],
                ],
                'labels' => ['unknown'],
            ]
        ];

        yield 'Returns types correctly' => [
            [
                [
                    'type' => 'some type',
                    'count' => 1,
                ],
                [
                    'type' => 'another type',
                    'count' => 2,
                ],
                [
                    'type' => 'different type',
                    'count' => 3,
                ],
                [
                    'type' => 'funny type',
                    'count' => 4,
                ],
                [
                    'type' => 'foo type',
                    'count' => 5,
                ],
                [
                    'type' => 'bar type',
                    'count' => 6,
                ],
                [
                    'type' => 'baz type',
                    'count' => 7,
                ],
            ],
            [
                'datasets' => [
                    [
                        'backgroundColor' => [
                            '#fc3',
                            '#ff8700',
                            '#a4276a',
                            '#1a568f',
                            '#4c7e3a',
                            '#69bbb5',
                            '#fc3',
                        ],
                        'data' => [
                            1,
                            2,
                            3,
                            4,
                            5,
                            6,
                            7,
                        ],
                    ],
                ],
                'labels' => [
                    'some type',
                    'another type',
                    'different type',
                    'funny type',
                    'foo type',
                    'bar type',
                    'baz type',
                ],
            ]
        ];
    }
}
