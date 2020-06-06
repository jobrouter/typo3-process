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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class TransferIdentifierVariableResolverTest extends TestCase
{
    /** @var TransferIdentifierVariableResolver */
    private $subject;

    /** @var Stub|ServerRequestInterface */
    private $serverRequestStub;

    protected function setUp(): void
    {
        $this->serverRequestStub = $this->createStub(ServerRequestInterface::class);
        $this->subject = new TransferIdentifierVariableResolver();
    }

    /**
     * @test
     * @dataProvider dataProviderForResolveVariables
     */
    public function resolveVariableCorrectly(string $value, string $transferIdentifier, string $expected): void
    {
        $event = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            $value,
            $transferIdentifier,
            $this->serverRequestStub
        );

        $this->subject->__invoke($event);

        self::assertSame($expected, $event->getValue());
    }

    public function dataProviderForResolveVariables(): \Generator
    {
        yield 'value with variable as only text' => [
            '{__transferIdentifier}',
            'some-identifier',
            'some-identifier',
        ];

        yield 'value as text with variable among other text' => [
            'foo {__transferIdentifier} bar',
            'some-identifier',
            'foo some-identifier bar',
        ];

        yield 'value as text with no variable' => [
            'foo bar',
            'some-identifier',
            'foo bar',
        ];

        yield 'value as text with another variable' => [
            '{__transferIdentifier1}',
            'some-identifier',
            '{__transferIdentifier1}',
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
            'some-identifier',
            $this->serverRequestStub
        );

        $this->subject->__invoke($event);
    }
}
