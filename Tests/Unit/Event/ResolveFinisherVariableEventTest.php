<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Tests\Event;

use Brotkrueml\JobRouterProcess\Event\ResolveFinisherVariableEvent;
use PHPUnit\Framework\TestCase;

class ResolveFinisherVariableEventTest extends TestCase
{
    /**
     * @test
     */
    public function gettersReturnValuesCorrectly(): void
    {
        $subject = new ResolveFinisherVariableEvent(
            42,
            'some-value',
            'some-identifier'
        );

        self::assertSame(42, $subject->getFieldType());
        self::assertSame('some-value', $subject->getValue());
        self::assertSame('some-identifier', $subject->getTransferIdentifier());
    }

    /**
     * @test
     */
    public function setValueSetsTheValueCorrectly(): void
    {
        $subject = new ResolveFinisherVariableEvent(
            42,
            'some-value',
            'some-identifier'
        );

        $subject->setValue('some-other-value');

        self::assertSame('some-other-value', $subject->getValue());
    }
}
