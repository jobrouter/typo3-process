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
use JobRouter\AddOn\Typo3Process\Widgets\Provider\TransferTypeChartDataProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

final class TransferTypeChartDataProviderTest extends TestCase
{
    private TransferRepository & Stub $transferRepositoryStub;
    private TransferTypeChartDataProvider $subject;

    protected function setUp(): void
    {
        if (! \interface_exists(ChartDataProviderInterface::class)) {
            self::markTestSkipped('Dashboard system extension not available');
        }

        $languageServiceStub = $this->createStub(LanguageService::class);
        $languageServiceStub
            ->method('sL')
            ->willReturn('unknown');

        $languageServiceFactoryStub = $this->createStub(LanguageServiceFactory::class);
        $languageServiceFactoryStub
            ->method('createFromUserPreferences')
            ->willReturn($languageServiceStub);

        $this->transferRepositoryStub = $this->createStub(TransferRepository::class);

        $this->subject = new TransferTypeChartDataProvider(
            $languageServiceFactoryStub,
            $this->transferRepositoryStub,
        );
        $this->subject->setNumberOfDays(13);

        $GLOBALS['BE_USER'] = $this->createStub(BackendUserAuthentication::class);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['BE_USER']);
    }

    #[Test]
    #[DataProvider('dataProviderForGetChartData')]
    public function getChartData(array $countTypesResult, array $expected): void
    {
        $this->transferRepositoryStub
            ->method('countTypes')
            ->with(13)
            ->willReturn($countTypesResult);

        self::assertSame($expected, $this->subject->getChartData());
    }

    public static function dataProviderForGetChartData(): \Generator
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
            ],
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
                        'backgroundColor' => ['#fabb00'],
                        'data' => [3],
                    ],
                ],
                'labels' => ['unknown'],
            ],
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
                            '#fabb00',
                            '#ff8700',
                            '#a4276a',
                            '#1a568f',
                            '#4c7e3a',
                            '#69bbb5',
                            '#fabb00',
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
            ],
        ];
    }
}
