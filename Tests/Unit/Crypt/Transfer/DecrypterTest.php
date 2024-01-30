<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Crypt\Transfer;

use Brotkrueml\JobRouterProcess\Crypt\Transfer\Decrypter;
use Brotkrueml\JobRouterProcess\Crypt\Transfer\EncryptedFieldsBitSet;
use Brotkrueml\JobRouterProcess\Domain\Dto\Transfer;
use Brotkrueml\JobRouterProcess\Exception\DecryptException;
use JobRouter\AddOn\Typo3Connector\Exception\CryptException;
use JobRouter\AddOn\Typo3Connector\Service\Crypt;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DecrypterTest extends TestCase
{
    private Crypt&MockObject $cryptServiceMock;
    private Decrypter $subject;

    protected function setUp(): void
    {
        $this->cryptServiceMock = $this->createMock(Crypt::class);
        $this->subject = new Decrypter($this->cryptServiceMock);
    }

    #[Test]
    public function decryptIfEncryptedReturnsTransferModelUntouchedIfItIsNotEncrypted(): void
    {
        $this->cryptServiceMock
            ->expects(self::never())
            ->method('decrypt');

        $transfer = new Transfer(12345567890, 42, 'some correlation');
        $actual = $this->subject->decryptIfEncrypted($transfer);

        self::assertSame($transfer, $actual);
    }

    #[Test]
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

        $transfer = new Transfer(12345567890, 42, 'some correlation');
        $transfer->setProcesstable('encrypted processtable');
        $transfer->setSummary('encrypted summary');
        $encryptedFields = new EncryptedFieldsBitSet();
        $encryptedFields->set(EncryptedFieldsBitSet::PROCESSTABLE);
        $encryptedFields->set(EncryptedFieldsBitSet::SUMMARY);
        $transfer->setEncryptedFields($encryptedFields);

        $actual = $this->subject->decryptIfEncrypted($transfer);

        self::assertNotSame($transfer, $actual);
        self::assertSame('processtable', $actual->getProcesstable());
        self::assertSame('summary', $actual->getSummary());
        self::assertSame(0, $actual->getEncryptedFields()->__toInt());
    }

    #[Test]
    public function decryptIfEncryptedDoesNotProcessFieldsWhichAreNotEncrypted(): void
    {
        $cryptServiceValueMap = [
            ['encrypted processtable', 'processtable'],
        ];
        $this->cryptServiceMock
            ->expects(self::once())
            ->method('decrypt')
            ->willReturnMap($cryptServiceValueMap);

        $transfer = new Transfer(12345567890, 42, 'some correlation');
        $transfer->setProcesstable('encrypted processtable');
        $encryptedFields = new EncryptedFieldsBitSet();
        $encryptedFields->set(EncryptedFieldsBitSet::PROCESSTABLE);
        $transfer->setEncryptedFields($encryptedFields);

        $actual = $this->subject->decryptIfEncrypted($transfer);

        self::assertNotSame($transfer, $actual);
        self::assertSame('processtable', $actual->getProcesstable());
        self::assertSame('', $actual->getSummary());
        self::assertSame(0, $actual->getEncryptedFields()->__toInt());
    }

    #[Test]
    public function decryptIfEncryptedThrowsExceptionWhenValueCannotBeDecrypted(): void
    {
        $this->expectException(DecryptException::class);
        $this->expectExceptionCode(1599323431);
        $this->expectExceptionMessage('Field "processtable" in transfer cannot be decrypted, reason: some crypt error');

        $this->cryptServiceMock
            ->method('decrypt')
            ->willThrowException(new CryptException('some crypt error'));

        $transfer = new Transfer(12345567890, 42, 'some correlation');
        $transfer->setProcesstable('encrypted processtable');
        $encryptedFields = new EncryptedFieldsBitSet();
        $encryptedFields->set(EncryptedFieldsBitSet::PROCESSTABLE);
        $transfer->setEncryptedFields($encryptedFields);

        $this->subject->decryptIfEncrypted($transfer);
    }
}
