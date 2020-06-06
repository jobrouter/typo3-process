<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Domain\VariableResolver;

use Brotkrueml\JobRouterProcess\Domain\VariableResolver\LanguageVariableResolver;
use Brotkrueml\JobRouterProcess\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterProcess\Event\ResolveFinisherVariableEvent;
use Brotkrueml\JobRouterProcess\Exception\VariableResolverException;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

class LanguageVariableResolverTest extends TestCase
{
    /** @var LanguageVariableResolver */
    private $subject;

    /** @var SiteLanguage */
    private $siteLanguage;

    protected function setUp(): void
    {
        $this->subject = new LanguageVariableResolver();

        /** @var $baseStub Stub|UriInterface */
        $baseStub = $this->createStub(UriInterface::class);
        $baseStub
            ->method('__toString')
            ->willReturn('https://www.example.org/');

        $this->siteLanguage = new SiteLanguage(
            42,
            'de_DE.UTF-8',
            $baseStub,
            [
                'title' => 'Some Title',
                'navigationTitle' => 'Some Navigation Title',
                'flag' => 'some-flag',
                'typo3Language' => 'default',
                'iso-639-1' => 'de',
                'hreflang' => 'de-de',
                'direction' => 'ltr',
            ]
        );

        $serverRequestStub = $this->createStub(ServerRequestInterface::class);
        $serverRequestStub
            ->method('getAttribute')
            ->with('language')
            ->willReturn($this->siteLanguage);

        $GLOBALS['TYPO3_REQUEST'] = $serverRequestStub;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function languageTwoLetterIsoCodeIsResolvedCorrectly(ResolveFinisherVariableEvent $event, $expected): void
    {
        $this->subject->__invoke($event);

        self::assertSame($expected, $event->getValue());
    }

    public function dataProvider(): iterable
    {
        yield 'language.twoLetterIsoCode is resolved' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__language.twoLetterIsoCode} bar',
                ''
            ),
            'foo de bar'
        ];

        yield 'language.title is resolved' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__language.title} bar',
                ''
            ),
            'foo Some Title bar'
        ];

        yield 'language.languageId is resolved' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__language.languageId} bar',
                ''
            ),
            'foo 42 bar'
        ];

        yield 'language.base is resolved' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__language.base} bar',
                ''
            ),
            'foo https://www.example.org/ bar'
        ];

        yield 'language.typo3Language is resolved' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__language.typo3Language} bar',
                ''
            ),
            'foo default bar'
        ];

        yield 'language.locale is resolved' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__language.locale} bar',
                ''
            ),
            'foo de_DE.UTF-8 bar'
        ];

        yield 'language.navigationTitle is resolved' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__language.navigationTitle} bar',
                ''
            ),
            'foo Some Navigation Title bar'
        ];

        yield 'language.hreflang is resolved' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__language.hreflang} bar',
                ''
            ),
            'foo de-de bar'
        ];

        yield 'language.direction is resolved' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__language.direction} bar',
                ''
            ),
            'foo ltr bar'
        ];

        yield 'language.flagIdentifier is resolved' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__language.flagIdentifier} bar',
                ''
            ),
            'foo some-flag bar'
        ];

        yield 'unknown language variable is returned untouched' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__language.unknown} bar',
                ''
            ),
            'foo {__language.unknown} bar'
        ];
    }

    /**
     * @test
     */
    public function multipleLanguageVariablesAreResolved(): void
    {
        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            '{__language.twoLetterIsoCode} {__language.direction}',
            ''
        );

        $this->subject->__invoke($event);

        self::assertSame('de ltr', $event->getValue());
    }

    /**
     * @test
     */
    public function onlyLanguageVariablesAreResolved(): void
    {
        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            '{__language1.twoLetterIsoCode}',
            ''
        );

        $this->subject->__invoke($event);

        self::assertSame('{__language1.twoLetterIsoCode}', $event->getValue());
    }

    /**
     * @test
     */
    public function languageKeyThatCannotMatchedIsIgnored(): void
    {
        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            '{__language.invalid key}',
            ''
        );

        $this->subject->__invoke($event);

        self::assertSame('{__language.invalid key}', $event->getValue());
    }

    /**
     * @test
     */
    public function wrongFieldTypeThrowsException(): void
    {
        $this->expectException(VariableResolverException::class);
        $this->expectExceptionCode(1582654966);
        $this->expectExceptionMessage('The value "{__language.twoLetterIsoCode}" contains a variable which can only be used in Text fields ("1"), type "2" used');

        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::INTEGER,
            '{__language.twoLetterIsoCode}',
            ''
        );

        $this->subject->__invoke($event);
    }

    /**
     * @test
     */
    public function languageCannotBeDeterminedLeavesVariablesUntouched(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            '{__language.twoLetterIsoCode}',
            ''
        );

        $this->subject->__invoke($event);

        self::assertSame('{__language.twoLetterIsoCode}', $event->getValue());
    }
}
