<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\VariableResolver;

use Brotkrueml\JobRouterProcess\Domain\VariableResolver\LocalisedLabelVariableResolver;
use Brotkrueml\JobRouterProcess\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterProcess\Event\ResolveFinisherVariableEvent;
use Brotkrueml\JobRouterProcess\Exception\VariableResolverException;
use Brotkrueml\JobRouterProcess\Language\TranslationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LocalisedLabelVariableResolverTest extends TestCase
{
    /** @var LocalisedLabelVariableResolver */
    private $subject;

    /** @var MockObject|TranslationService */
    private $translationService;

    protected function setUp(): void
    {
        $this->translationService = $this->createMock(TranslationService::class);
        $this->subject = new LocalisedLabelVariableResolver($this->translationService);
    }

    /**
     * @test
     */
    public function oneLocalisedLabelIsResolved(): void
    {
        $this->translationService
            ->expects(self::once())
            ->method('translate')
            ->with('LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:some.label')
            ->willReturn('localised some label');

        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            'foo {__LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:some.label} bar',
            ''
        );

        $this->subject->resolve($event);

        self::assertSame('foo localised some label bar', $event->getValue());
    }

    /**
     * @test
     */
    public function twoLocalisedLabelAreResolved(): void
    {
        $this->translationService
            ->expects(self::at(0))
            ->method('translate')
            ->with('LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:some.label')
            ->willReturn('localised some label');

        $this->translationService
            ->expects(self::at(1))
            ->method('translate')
            ->with('LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:another.label')
            ->willReturn('localised another label');

        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            'foo {__LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:some.label} bar {__LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:another.label}',
            ''
        );

        $this->subject->resolve($event);

        self::assertSame('foo localised some label bar localised another label', $event->getValue());
    }

    /**
     * @test
     */
    public function noLocalisedLabelFoundThenValueIsUntouched(): void
    {
        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            'foo bar',
            ''
        );

        $this->subject->resolve($event);

        self::assertSame(
            'foo bar',
            $event->getValue()
        );
    }

    /**
     * @test
     */
    public function localisedLabelIsNotFoundThenValueIsUntouched(): void
    {
        $this->translationService
            ->expects(self::once())
            ->method('translate')
            ->willReturn('');

        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            'foo {__LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:not.existing} bar',
            ''
        );

        $this->subject->resolve($event);

        self::assertSame(
            'foo {__LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:not.existing} bar',
            $event->getValue()
        );
    }

    /**
     * @test
     */
    public function wrongVariableDescriptionThenValueIsUntouched(): void
    {
        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            'foo {__LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:not.existing bar',
            ''
        );

        $this->subject->resolve($event);

        self::assertSame(
            'foo {__LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:not.existing bar',
            $event->getValue()
        );
    }

    /**
     * @test
     */
    public function resolveThrowsExceptionWithFieldTypeNotString(): void
    {
        $this->expectException(VariableResolverException::class);
        $this->expectExceptionCode(1582907006);
        $this->expectExceptionMessage('The value "{__LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:some.label}" contains a localised label which can only be used in Text fields ("1"), type "2" used');

        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::INTEGER,
            '{__LLL:EXT:some_ext/Resources/Private/Language/locallang.xlf:some.label}',
            ''
        );

        $this->subject->resolve($event);
    }
}
