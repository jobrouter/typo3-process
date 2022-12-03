<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Transfer;

use Brotkrueml\JobRouterProcess\Exception\DeleteException;
use Brotkrueml\JobRouterProcess\Transfer\Deleter;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class DeleterTest extends TestCase
{
    private Deleter $subject;

    /**
     * @var Stub&QueryBuilder
     */
    private $queryBuilderStub;

    protected function setUp(): void
    {
        self::markTestSkipped('Deleter class will be reworked!');

        $this->queryBuilderStub = $this->createStub(QueryBuilder::class);
        $this->subject = new Deleter($this->queryBuilderStub);
        $this->subject->setLogger(new NullLogger());
    }

    /**
     * @test
     */
    public function runReturnsTheAffectedRows(): void
    {
        $this->queryBuilderStub
            ->method('delete')
            ->with('tx_jobrouterprocess_domain_model_transfer')
            ->willReturn($this->queryBuilderStub);
        $this->queryBuilderStub
            ->method('where')
            ->willReturn($this->queryBuilderStub);
        $this->queryBuilderStub
            ->method('execute')
            ->willReturn(42);

        self::assertSame(42, $this->subject->run(30));
    }

    /**
     * @test
     */
    public function runThrowsAnExceptionWhenQueryFails(): void
    {
        $this->expectException(DeleteException::class);
        $this->expectExceptionCode(1582133383);
        $this->expectExceptionMessage('Error on clean up of old transfers: Some foo error');

        $this->queryBuilderStub
            ->method('delete')
            ->willThrowException(new \Exception('Some foo error'));

        $this->subject->run(30);
    }
}
