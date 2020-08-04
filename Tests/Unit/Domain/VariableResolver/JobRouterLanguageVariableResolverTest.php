<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\VariableResolver;

use Brotkrueml\JobRouterProcess\Domain\VariableResolver\JobRouterLanguageVariableResolver;
use Brotkrueml\JobRouterProcess\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterProcess\Event\ResolveFinisherVariableEvent;
use Brotkrueml\JobRouterProcess\Exception\VariableResolverException;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

class JobRouterLanguageVariableResolverTest extends TestCase
{
    /** @var JobRouterLanguageVariableResolver */
    private $subject;

    /** @var Stub|ServerRequestInterface */
    private $serverRequestStub;

    protected function setUp(): void
    {
        $this->subject = new JobRouterLanguageVariableResolver();

        /** @var $baseStub Stub|UriInterface */
        $baseStub = $this->createStub(UriInterface::class);
        $baseStub
            ->method('__toString')
            ->willReturn('https://www.example.org/');

        $this->serverRequestStub = $this->createStub(ServerRequestInterface::class);
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param string $value
     * @param $isoCode
     * @param string $expected
     */
    public function jobRouterLanguageVariableIsResolvedCorrectly(string $value, $isoCode, string $expected): void
    {
        $siteLanguage = new SiteLanguage(
            1,
            '',
            $this->createStub(UriInterface::class),
            ['iso-639-1' => $isoCode]
        );

        $this->serverRequestStub
            ->method('getAttribute')
            ->with('language')
            ->willReturn($siteLanguage);

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
        yield 'jobRouterLanguage for Arabic is resolved' => [
            '{__jobRouterLanguage}',
            'ar',
            'arabic',
        ];

        yield 'jobRouterLanguage for Chinese is resolved' => [
            '{__jobRouterLanguage}',
            'zh',
            'chinese',
        ];

        yield 'jobRouterLanguage for Czech is resolved' => [
            '{__jobRouterLanguage}',
            'cs',
            'czech',
        ];

        yield 'jobRouterLanguage for Danish is resolved' => [
            '{__jobRouterLanguage}',
            'da',
            'danish',
        ];

        yield 'jobRouterLanguage for Dutch is resolved' => [
            '{__jobRouterLanguage}',
            'nl',
            'dutch',
        ];

        yield 'jobRouterLanguage for English is resolved' => [
            '{__jobRouterLanguage}',
            'en',
            'english',
        ];

        yield 'jobRouterLanguage for German is resolved' => [
            '{__jobRouterLanguage}',
            'de',
            'german',
        ];

        yield 'jobRouterLanguage for Finnish is resolved' => [
            '{__jobRouterLanguage}',
            'fi',
            'finnish',
        ];

        yield 'jobRouterLanguage for French is resolved' => [
            '{__jobRouterLanguage}',
            'fr',
            'french',
        ];

        yield 'jobRouterLanguage for Hungarian is resolved' => [
            '{__jobRouterLanguage}',
            'hu',
            'hungarian',
        ];

        yield 'jobRouterLanguage for Italian is resolved' => [
            '{__jobRouterLanguage}',
            'it',
            'italian',
        ];

        yield 'jobRouterLanguage for Japanese is resolved' => [
            '{__jobRouterLanguage}',
            'ja',
            'japanese',
        ];

        yield 'jobRouterLanguage for Polish is resolved' => [
            '{__jobRouterLanguage}',
            'pl',
            'polish',
        ];

        yield 'jobRouterLanguage for Romanian is resolved' => [
            '{__jobRouterLanguage}',
            'ro',
            'romanian',
        ];

        yield 'jobRouterLanguage for Russian is resolved' => [
            '{__jobRouterLanguage}',
            'ru',
            'russian',
        ];

        yield 'jobRouterLanguage for Slovak is resolved' => [
            '{__jobRouterLanguage}',
            'sk',
            'slovak',
        ];

        yield 'jobRouterLanguage for Slovenian is resolved' => [
            '{__jobRouterLanguage}',
            'sl',
            'slovenian',
        ];

        yield 'jobRouterLanguage for Spanish is resolved' => [
            '{__jobRouterLanguage}',
            'es',
            'spanish',
        ];

        yield 'jobRouterLanguage for Turkish is resolved' => [
            '{__jobRouterLanguage}',
            'tr',
            'turkish',
        ];

        yield 'jobRouterLanguage is resolved to empty on non-supported language' => [
            '{__jobRouterLanguage}',
            'zz',
            '',
        ];

        yield 'jobRouterLanguage is resolved with prefix and postfix' => [
            'foo {__jobRouterLanguage} bar',
            'en',
            'foo english bar',
        ];

        yield 'jobRouterLanguage is resolved twice' => [
            'foo {__jobRouterLanguage} bar {__jobRouterLanguage} qux',
            'en',
            'foo english bar english qux',
        ];

        yield 'value is untouched when variable is not set' => [
            'foo bar',
            'en',
            'foo bar',
        ];
    }

    /**
     * @test
     */
    public function wrongFieldTypeThrowsException(): void
    {
        $this->expectException(VariableResolverException::class);
        $this->expectExceptionCode(1594214444);
        $this->expectExceptionMessage('The value "{__jobRouterLanguage}" contains a variable which can only be used in Text fields ("1"), type "2" used');

        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::INTEGER,
            '{__jobRouterLanguage}',
            '',
            [],
            $this->serverRequestStub
        );

        $this->subject->__invoke($event);
    }

    /**
     * @test
     */
    public function languageCannotBeDeterminedThenVariableIsRemoved(): void
    {
        $this->serverRequestStub
            ->method('getAttribute')
            ->with('language')
            ->willReturn(null);

        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            '{__jobRouterLanguage}',
            '',
            [],
            $this->serverRequestStub
        );

        $this->subject->__invoke($event);

        self::assertSame('', $event->getValue());
    }
}
