<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Unit\Crypt\Transfer;

use JobRouter\AddOn\Typo3Connector\Exception\CryptException;
use JobRouter\AddOn\Typo3Connector\Service\Crypt;
use JobRouter\AddOn\Typo3Process\Crypt\Transfer\EncryptedFieldsBitSet;
use JobRouter\AddOn\Typo3Process\Crypt\Transfer\Encrypter;
use JobRouter\AddOn\Typo3Process\Domain\Dto\Transfer;
use JobRouter\AddOn\Typo3Process\Extension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

final class EncrypterTest extends TestCase
{
    private ExtensionConfiguration&Stub $extensionConfigurationStub;
    private Crypt&MockObject $cryptServiceMock;
    private Encrypter $subject;

    protected function setUp(): void
    {
        $this->extensionConfigurationStub = $this->createStub(ExtensionConfiguration::class);
        $this->cryptServiceMock = $this->createMock(Crypt::class);

        $this->subject = new Encrypter($this->cryptServiceMock, $this->extensionConfigurationStub, new NullLogger());
    }

    #[Test]
    public function encryptIfConfiguredDoesReturnsTransferModelUntouchedIfEncryptionIsEnabled(): void
    {
        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY, Extension::ENCRYPT_DATA_CONFIG_IDENTIFIER)
            ->willReturn(false);

        $transfer = new Transfer(1234567890, 42, 'some-correlation');
        $actual = $this->subject->encryptIfConfigured($transfer);

        self::assertSame($transfer, $actual);
    }

    #[Test]
    public function encryptIfConfiguredEncryptsTransferModelDataIfEncryptionIsEnabled(): void
    {
        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY, Extension::ENCRYPT_DATA_CONFIG_IDENTIFIER)
            ->willReturn(true);

        $cryptServiceReturnMap = [
            ['processtable',  'encrypted processtable'],
            ['summary', 'encrypted summary'],
        ];
        $this->cryptServiceMock
            ->expects(self::exactly(2))
            ->method('encrypt')
            ->willReturnMap($cryptServiceReturnMap);

        $transfer = new Transfer(1234567890, 42, 'some-correlation');
        $transfer->setProcesstable('processtable');
        $transfer->setSummary('summary');
        $actual = $this->subject->encryptIfConfigured($transfer);

        $expectedEncryptedFields = new EncryptedFieldsBitSet();
        $expectedEncryptedFields->set(EncryptedFieldsBitSet::PROCESSTABLE);
        $expectedEncryptedFields->set(EncryptedFieldsBitSet::SUMMARY);

        self::assertNotSame($actual, $transfer);
        self::assertSame($expectedEncryptedFields->__toInt(), $actual->getEncryptedFields()->__toInt());
        self::assertSame('encrypted processtable', $actual->getProcesstable());
        self::assertSame('encrypted summary', $actual->getSummary());
    }

    #[Test]
    public function encryptIfConfiguredDoesNotProcessFieldsWhichAreEmpty(): void
    {
        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY, Extension::ENCRYPT_DATA_CONFIG_IDENTIFIER)
            ->willReturn(true);

        $cryptServiceReturnMap = [
            ['processtable',  'encrypted processtable'],
        ];
        $this->cryptServiceMock
            ->expects(self::once())
            ->method('encrypt')
            ->willReturnMap($cryptServiceReturnMap);

        $transfer = new Transfer(1234567890, 42, 'some-correlation');
        $transfer->setProcesstable('processtable');
        $actual = $this->subject->encryptIfConfigured($transfer);

        $expectedEncryptedFields = new EncryptedFieldsBitSet();
        $expectedEncryptedFields->set(EncryptedFieldsBitSet::PROCESSTABLE);

        self::assertNotSame($actual, $transfer);
        self::assertSame($expectedEncryptedFields->__toInt(), $actual->getEncryptedFields()->__toInt());
        self::assertSame('encrypted processtable', $actual->getProcesstable());
        self::assertSame('', $actual->getSummary());
    }

    #[Test]
    public function encryptIfConfiguredReturnsEncryptedTransferIfDataCannotBeEncrypted(): void
    {
        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY, Extension::ENCRYPT_DATA_CONFIG_IDENTIFIER)
            ->willReturn(true);

        $this->cryptServiceMock
            ->method('encrypt')
            ->willThrowException((new CryptException('some crypt error')));

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock
            ->expects(self::once())
            ->method('warning')
            ->with('Field "processtable" in transfer DTO cannot be encrypted, it will be stored unencrypted, reason: some crypt error');

        $subject = new Encrypter($this->cryptServiceMock, $this->extensionConfigurationStub, $loggerMock);

        $transfer = new Transfer(1234567890, 42, 'some-correlation');
        $transfer->setProcesstable('processtable');
        $actual = $subject->encryptIfConfigured($transfer);

        self::assertNotSame($transfer, $actual);
        self::assertSame('processtable', $actual->getProcesstable());
        self::assertSame(0, $actual->getEncryptedFields()->__toInt());
    }
}
