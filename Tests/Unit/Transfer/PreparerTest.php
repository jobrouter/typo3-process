<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Transfer;

use Brotkrueml\JobRouterProcess\Crypt\Transfer\Encrypter;
use Brotkrueml\JobRouterProcess\Domain\Dto\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Exception\PrepareException;
use Brotkrueml\JobRouterProcess\Transfer\Preparer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class PreparerTest extends TestCase
{
    private Preparer $subject;
    private Encrypter&MockObject $encrypterMock;
    private TransferRepository&MockObject $transferRepositoryMock;

    protected function setUp(): void
    {
        $this->encrypterMock = $this->createMock(Encrypter::class);
        $this->transferRepositoryMock = $this->createMock(TransferRepository::class);

        $this->subject = new Preparer(
            $this->encrypterMock,
            new NullLogger(),
            $this->transferRepositoryMock,
        );
    }

    #[Test]
    public function storePersistsRecordCorrectly(): void
    {
        $transfer = new Transfer(1234567890, 42, 'some-correlation');
        $transfer->setProcesstable('some data');

        $this->encrypterMock
            ->expects(self::once())
            ->method('encryptIfConfigured')
            ->willReturn($transfer);
        $this->transferRepositoryMock
            ->expects(self::once())
            ->method('add')
            ->willReturn(1);

        $this->subject->store($transfer);
    }

    #[Test]
    public function storeThrowsExceptionOnError(): void
    {
        $this->expectException(PrepareException::class);
        $this->expectExceptionCode(1581278897);
        $this->expectExceptionMessage('Transfer record cannot be written, see log file for details.');

        $this->encrypterMock
            ->expects(self::once())
            ->method('encryptIfConfigured')
            ->willReturn(new Transfer(1234567890, 42, 'some-correlation'));
        $this->transferRepositoryMock
            ->method('add')
            ->willReturn(0);

        $this->subject->store(new Transfer(1234567890, 42, 'some-correlation'));
    }
}
