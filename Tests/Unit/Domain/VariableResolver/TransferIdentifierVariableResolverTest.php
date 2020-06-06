<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\VariableResolver;

use Brotkrueml\JobRouterProcess\Domain\VariableResolver\TransferIdentifierVariableResolver;
use Brotkrueml\JobRouterProcess\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterProcess\Event\ResolveFinisherVariableEvent;
use Brotkrueml\JobRouterProcess\Exception\VariableResolverException;
use PHPUnit\Framework\TestCase;

class TransferIdentifierVariableResolverTest extends TestCase
{
    /** @var TransferIdentifierVariableResolver */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new TransferIdentifierVariableResolver();
    }

    /**
     * @test
     * @dataProvider dataProviderForResolveVariables
     */
    public function resolveVariableCorrectly(ResolveFinisherVariableEvent $event, string $expected): void
    {
        $this->subject->__invoke($event);

        self::assertSame($expected, $event->getValue());
    }

    public function dataProviderForResolveVariables(): iterable
    {
        yield 'value with variable as only text' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                '{__transferIdentifier}',
                'some-identifier'
            ),
            'some-identifier'
        ];

        yield 'value as text with variable among other text' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo {__transferIdentifier} bar',
                'some-identifier'
            ),
            'foo some-identifier bar'
        ];

        yield 'value as text with no variable' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                'foo bar',
                'some-identifier'
            ),
            'foo bar'
        ];

        yield 'value as text with another variable' => [
            new ResolveFinisherVariableEvent(
                FieldTypeEnumeration::TEXT,
                '{__transferIdentifier1}',
                'some-identifier'
            ),
            '{__transferIdentifier1}'
        ];
    }

    /**
     * @test
     */
    public function resolveThrowsExceptionWithFieldTypeNotString(): void
    {
        $this->expectException(VariableResolverException::class);
        $this->expectExceptionCode(1582654966);
        $this->expectExceptionMessage('The "{__transferIdentifier}" variable can only be used in Text fields ("1"), type "2" used');

        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::INTEGER,
            '{__transferIdentifier}',
            'some-identifier'
        );

        $this->subject->__invoke($event);
    }
}
