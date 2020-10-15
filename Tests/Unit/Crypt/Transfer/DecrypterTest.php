<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Crypt\Transfer;

use Brotkrueml\JobRouterConnector\Exception\CryptException;
use Brotkrueml\JobRouterConnector\Service\Crypt;
use Brotkrueml\JobRouterProcess\Crypt\Transfer\Decrypter;
use Brotkrueml\JobRouterProcess\Crypt\Transfer\EncryptedFieldsBitSet;
use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Exception\DecryptException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DecrypterTest extends TestCase
{
    /** @var Crypt|MockObject */
    private $cryptServiceMock;

    /** @var Decrypter */
    private $subject;

    protected function setUp(): void
    {
        $this->cryptServiceMock = $this->createMock(Crypt::class);
        $this->subject = new Decrypter($this->cryptServiceMock);
    }

    /**
     * @test
     */
    public function decryptIfEncryptedReturnsTransferModelUntouchedIfItIsNotEncrypted(): void
    {
        $this->cryptServiceMock
            ->expects(self::never())
            ->method('decrypt');

        $transfer = new Transfer();
        $actual = $this->subject->decryptIfEncrypted($transfer);

        self::assertSame($transfer, $actual);
    }

    /**
     * @test
     */
    public function decryptIfEncryptedDecryptsEncryptedTransferFieldsCorrectly(): void
    {
        $cryptServiceValueMap = [
            ['encrypted processtable', 'processtable'],
            ['encrypted summary', 'summary'],
        ];
        $this->cryptServiceMock
            ->expects(self::exactly(2))
            ->method('decrypt')
            ->willReturnMap($cryptServiceValueMap);

        $transfer = new Transfer();
        $transfer->setProcesstable('encrypted processtable');
        $transfer->setSummary('encrypted summary');
        $encryptedFields = new EncryptedFieldsBitSet();
        $encryptedFields->set(EncryptedFieldsBitSet::PROCESSTABLE);
        $encryptedFields->set(EncryptedFieldsBitSet::SUMMARY);
        $transfer->setEncryptedFields($encryptedFields->__toInt());

        $actual = $this->subject->decryptIfEncrypted($transfer);

        self::assertNotSame($transfer, $actual);
        self::assertSame('processtable', $actual->getProcesstable());
        self::assertSame('summary', $actual->getSummary());
        self::assertSame(0, $actual->getEncryptedFields());
    }

    /**
     * @test
     */
    public function decryptIfEncryptedDoesNotProcessFieldsWhichAreNotEncrypted(): void
    {
        $cryptServiceValueMap = [
            ['encrypted processtable', 'processtable'],
        ];
        $this->cryptServiceMock
            ->expects(self::once())
            ->method('decrypt')
            ->willReturnMap($cryptServiceValueMap);

        $transfer = new Transfer();
        $transfer->setProcesstable('encrypted processtable');
        $encryptedFields = new EncryptedFieldsBitSet();
        $encryptedFields->set(EncryptedFieldsBitSet::PROCESSTABLE);
        $transfer->setEncryptedFields($encryptedFields->__toInt());

        $actual = $this->subject->decryptIfEncrypted($transfer);

        self::assertNotSame($transfer, $actual);
        self::assertSame('processtable', $actual->getProcesstable());
        self::assertSame('', $actual->getSummary());
        self::assertSame(0, $actual->getEncryptedFields());
    }

    /**
     * @test
     */
    public function decryptIfEncryptedThrowsExceptionWhenValueCannotBeDecrypted(): void
    {
        $this->expectException(DecryptException::class);
        $this->expectExceptionCode(1599323431);
        $this->expectExceptionMessage('Field "processtable" in transfer with identifier "some identifier" cannot be decrypted, reason: some crypt error');

        $this->cryptServiceMock
            ->method('decrypt')
            ->willThrowException(new CryptException('some crypt error'));

        $transfer = new Transfer();
        $transfer->setIdentifier('some identifier');
        $transfer->setProcesstable('encrypted processtable');
        $encryptedFields = new EncryptedFieldsBitSet();
        $encryptedFields->set(EncryptedFieldsBitSet::PROCESSTABLE);
        $transfer->setEncryptedFields($encryptedFields->__toInt());

        $this->subject->decryptIfEncrypted($transfer);
    }
}
