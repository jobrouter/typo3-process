<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Event;

use Brotkrueml\JobRouterProcess\Event\ResolveFinisherVariableEvent;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ResolveFinisherVariableEventTest extends TestCase
{
    /** @var Stub|ServerRequestInterface */
    private $serverRequestStub;

    protected function setUp(): void
    {
        $this->serverRequestStub = $this->createStub(ServerRequestInterface::class);
    }

    /**
     * @test
     */
    public function gettersReturnValuesCorrectly(): void
    {
        $subject = new ResolveFinisherVariableEvent(
            42,
            'some-value',
            'some-identifier',
            $this->serverRequestStub
        );

        self::assertSame(42, $subject->getFieldType());
        self::assertSame('some-value', $subject->getValue());
        self::assertSame('some-identifier', $subject->getTransferIdentifier());
        self::assertSame($this->serverRequestStub, $subject->getRequest());
    }

    /**
     * @test
     */
    public function setValueSetsTheValueCorrectly(): void
    {
        $subject = new ResolveFinisherVariableEvent(
            42,
            'some-value',
            'some-identifier',
            $this->serverRequestStub
        );

        $subject->setValue('some-other-value');

        self::assertSame('some-other-value', $subject->getValue());
    }
}
