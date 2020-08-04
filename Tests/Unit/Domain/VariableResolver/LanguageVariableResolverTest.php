<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\VariableResolver;

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

    /** @var Stub|ServerRequestInterface */
    private $serverRequestStub;

    protected function setUp(): void
    {
        $this->subject = new LanguageVariableResolver();

        /** @var $baseStub Stub|UriInterface */
        $baseStub = $this->createStub(UriInterface::class);
        $baseStub
            ->method('__toString')
            ->willReturn('https://www.example.org/');

        $siteLanguage = new SiteLanguage(
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

        $this->serverRequestStub = $this->createStub(ServerRequestInterface::class);
        $this->serverRequestStub
            ->method('getAttribute')
            ->with('language')
            ->willReturn($siteLanguage);
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function languageVariablesAreResolvedCorrectly(string $value, string $expected): void
    {
        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            $value,
            '',
            [],
            $this->serverRequestStub
        );

        $this->subject->__invoke($event);

        self::assertSame($expected, $event->getValue());
    }

    public function dataProvider(): \Generator
    {
        yield 'language.twoLetterIsoCode is resolved' => [
            'foo {__language.twoLetterIsoCode} bar',
            'foo de bar',
        ];

        yield 'language.title is resolved' => [
            'foo {__language.title} bar',
            'foo Some Title bar',
        ];

        yield 'language.languageId is resolved' => [
            'foo {__language.languageId} bar',
            'foo 42 bar',
        ];

        yield 'language.base is resolved' => [
            'foo {__language.base} bar',
            'foo https://www.example.org/ bar',
        ];

        yield 'language.typo3Language is resolved' => [
            'foo {__language.typo3Language} bar',
            'foo default bar',
        ];

        yield 'language.locale is resolved' => [
            'foo {__language.locale} bar',
            'foo de_DE.UTF-8 bar',
        ];

        yield 'language.navigationTitle is resolved' => [
            'foo {__language.navigationTitle} bar',
            'foo Some Navigation Title bar',
        ];

        yield 'language.hreflang is resolved' => [
            'foo {__language.hreflang} bar',
            'foo de-de bar',
        ];

        yield 'language.direction is resolved' => [
            'foo {__language.direction} bar',
            'foo ltr bar',
        ];

        yield 'language.flagIdentifier is resolved' => [
            'foo {__language.flagIdentifier} bar',
            'foo some-flag bar',
        ];

        yield 'unknown language variable is returned untouched' => [
            'foo {__language.unknown} bar',
            'foo {__language.unknown} bar',
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
            '',
            [],
            $this->serverRequestStub
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
            '',
            [],
            $this->serverRequestStub
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
            '',
            [],
            $this->serverRequestStub
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
            '',
            [],
            $this->serverRequestStub
        );

        $this->subject->__invoke($event);
    }

    /**
     * @test
     */
    public function languageCannotBeDeterminedLeavesVariablesUntouched(): void
    {
        $this->serverRequestStub = $this->createStub(ServerRequestInterface::class);
        $this->serverRequestStub
            ->method('getAttribute')
            ->with('language')
            ->willReturn(null);

        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            '{__language.twoLetterIsoCode}',
            '',
            [],
            $this->serverRequestStub
        );

        $this->subject->__invoke($event);

        self::assertSame('{__language.twoLetterIsoCode}', $event->getValue());
    }
}
