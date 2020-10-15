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
use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Exception\PrepareException;
use Brotkrueml\JobRouterProcess\Transfer\Preparer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class PreparerTest extends TestCase
{
    /** @var Preparer */
    private $subject;

    /** @var MockObject|PersistenceManager */
    private $persistenceManagerMock;

    /** @var MockObject|Encrypter */
    private $encrypterMock;

    /** @var MockObject|TransferRepository */
    private $transferRepositoryMock;

    protected function setUp(): void
    {
        $this->persistenceManagerMock = $this->createMock(PersistenceManager::class);
        $this->encrypterMock = $this->createMock(Encrypter::class);
        $this->transferRepositoryMock = $this->createMock(TransferRepository::class);

        $this->subject = new Preparer(
            $this->persistenceManagerMock,
            $this->encrypterMock,
            $this->transferRepositoryMock
        );
        $this->subject->setLogger(new NullLogger());
    }

    /**
     * @test
     */
    public function storePersistsRecordCorrectly(): void
    {
        $transfer = new Transfer();
        $transfer->setStepUid(42);
        $transfer->setCorrelationId('some identifier');
        $transfer->setProcesstable('some data');

        $this->persistenceManagerMock
            ->expects(self::once())
            ->method('persistAll');
        $this->encrypterMock
            ->expects(self::once())
            ->method('encryptIfConfigured')
            ->willReturn($transfer);
        $this->transferRepositoryMock
            ->expects(self::once())
            ->method('add');

        $this->subject->store($transfer);
    }

    /**
     * @test
     */
    public function storeThrowsExceptionOnError(): void
    {
        $this->expectException(PrepareException::class);
        $this->expectExceptionCode(1581278897);
        $this->expectExceptionMessage('Transfer record cannot be written');

        $this->encrypterMock
            ->expects(self::once())
            ->method('encryptIfConfigured')
            ->willReturn(new Transfer());
        $this->transferRepositoryMock
            ->method('add')
            ->willThrowException(new \Exception());

        $this->subject->store(new Transfer());
    }
}
