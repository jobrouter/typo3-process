<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\VariableResolver;

use Brotkrueml\JobRouterProcess\Domain\VariableResolver\VariableResolver;
use Brotkrueml\JobRouterProcess\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterProcess\Event\ResolveFinisherVariableEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

class VariableResolverTest extends TestCase
{
    /** @var Stub|ServerRequestInterface */
    private $requestStub;

    /** @var MockObject|EventDispatcherInterface */
    private $eventDispatcherMock;

    /** @var VariableResolver */
    private $subject;

    protected function setUp(): void
    {
        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->subject = new VariableResolver($this->eventDispatcherMock);
        $this->subject->setTransferIdentifier('some identifier');
        $this->subject->setFormValues(['foo' => 'bar']);
        $this->subject->setRequest($this->requestStub);
    }

    /**
     * @test
     */
    public function resolveReturnsValueUntouchedIfNotContainingVariable(): void
    {
        $this->eventDispatcherMock
            ->expects(self::never())
            ->method('dispatch');

        $actual = $this->subject->resolve(FieldTypeEnumeration::TEXT, 'value without variable');

        self::assertSame('value without variable', $actual);
    }

    /**
     * @test
     */
    public function resolveCallsEventDispatcherIfVariableIsAvailable(): void
    {
        $returnedEvent = new ResolveFinisherVariableEvent(
            FieldTypeEnumeration::TEXT,
            'resolved value',
            'some identifier',
            ['foo' => 'bar'],
            $this->requestStub
        );

        $this->eventDispatcherMock
            ->expects(self::once())
            ->method('dispatch')
            ->willReturn($returnedEvent);

        $actual = $this->subject->resolve(FieldTypeEnumeration::TEXT, '{__variable} value');

        self::assertSame('resolved value', $actual);
    }
}
