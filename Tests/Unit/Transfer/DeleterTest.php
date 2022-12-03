<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Transfer;

use Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterProcess\Exception\DeleteException;
use Brotkrueml\JobRouterProcess\Transfer\Deleter;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class DeleterTest extends TestCase
{
    /**
     * @var TransferRepository&Stub
     */
    private $transferRepositoryStub;
    private Deleter $subject;

    protected function setUp(): void
    {
        self::markTestSkipped('Should be moved to functional tests.');

        $this->transferRepositoryStub = $this->createSTub(TransferRepository::class);

        $this->subject = new Deleter($this->transferRepositoryStub);
        $this->subject->setLogger(new NullLogger());
    }

    /**
     * @test
     */
    public function runReturnsTheDeletedTransfers(): void
    {
        $this->transferRepositoryStub
            ->method('deleteTransfers')
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

        $this->transferRepositoryStub
            ->method('deleteTransfers')
            ->willThrowException(new \Exception('Some foo error'));

        $this->subject->run(30);
    }
}
