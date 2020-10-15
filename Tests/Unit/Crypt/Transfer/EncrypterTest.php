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
use Brotkrueml\JobRouterProcess\Crypt\Transfer\EncryptedFieldsBitSet;
use Brotkrueml\JobRouterProcess\Crypt\Transfer\Encrypter;
use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Extension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class EncrypterTest extends TestCase
{
    /** @var Stub|ExtensionConfiguration */
    private $extensionConfigurationStub;

    /**
     * @var MockObject|Crypt
     */
    private $cryptServiceMock;

    /** @var Encrypter */
    private $subject;

    protected function setUp(): void
    {
        $this->extensionConfigurationStub = $this->createStub(ExtensionConfiguration::class);
        $this->cryptServiceMock = $this->createMock(Crypt::class);

        $this->subject = new Encrypter($this->cryptServiceMock, $this->extensionConfigurationStub);
    }

    /**
     * @test
     */
    public function encryptIfConfiguredDoesReturnsTransferModelUntouchedIfEncryptionIsEnabled(): void
    {
        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY, Extension::ENCRYPT_DATA_CONFIG_IDENTIFIER)
            ->willReturn(false);

        $transfer = new Transfer();
        $actual = $this->subject->encryptIfConfigured($transfer);

        self::assertSame($transfer, $actual);
    }

    /**
     * @test
     */
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

        $transfer = new Transfer();
        $transfer->setProcesstable('processtable');
        $transfer->setSummary('summary');
        $actual = $this->subject->encryptIfConfigured($transfer);

        $expectedEncryptedFields = new EncryptedFieldsBitSet();
        $expectedEncryptedFields->set(EncryptedFieldsBitSet::PROCESSTABLE);
        $expectedEncryptedFields->set(EncryptedFieldsBitSet::SUMMARY);

        self::assertNotSame($actual, $transfer);
        self::assertSame($expectedEncryptedFields->__toInt(), $actual->getEncryptedFields());
        self::assertSame('encrypted processtable', $actual->getProcesstable());
        self::assertSame('encrypted summary', $actual->getSummary());
    }

    /**
     * @test
     */
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

        $transfer = new Transfer();
        $transfer->setProcesstable('processtable');
        $actual = $this->subject->encryptIfConfigured($transfer);

        $expectedEncryptedFields = new EncryptedFieldsBitSet();
        $expectedEncryptedFields->set(EncryptedFieldsBitSet::PROCESSTABLE);

        self::assertNotSame($actual, $transfer);
        self::assertSame($expectedEncryptedFields->__toInt(), $actual->getEncryptedFields());
        self::assertSame('encrypted processtable', $actual->getProcesstable());
        self::assertSame('', $actual->getSummary());
    }

    /**
     * @test
     */
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
            ->with('Field "processtable" in transfer with uid "38" cannot be encrypted, it will be stored unencrypted, reason: some crypt error');
        $this->subject->setLogger($loggerMock);

        $transfer = new Transfer();
        $transfer->_setProperty('uid', 38);
        $transfer->setCorrelationId('some identifier');
        $transfer->setProcesstable('processtable');
        $actual = $this->subject->encryptIfConfigured($transfer);

        self::assertNotSame($transfer, $actual);
        self::assertSame('processtable', $actual->getProcesstable());
        self::assertSame(0, $actual->getEncryptedFields());
    }
}
