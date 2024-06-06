<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Crypt\Transfer;

use JobRouter\AddOn\Typo3Connector\Exception\CryptException;
use JobRouter\AddOn\Typo3Connector\Service\Crypt;
use JobRouter\AddOn\Typo3Process\Domain\Dto\Transfer;
use JobRouter\AddOn\Typo3Process\Extension;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class Encrypter
{
    // @todo Get rid of those class properties (see also phpstan baseline)
    private ?Transfer $encryptedTransfer = null;
    private ?EncryptedFieldsBitSet $encryptedFields = null;

    public function __construct(
        private readonly Crypt $cryptService,
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly LoggerInterface $logger,
    ) {}

    public function encryptIfConfigured(Transfer $transfer): Transfer
    {
        if (! $this->extensionConfiguration->get(Extension::KEY, Extension::ENCRYPT_DATA_CONFIG_IDENTIFIER)) {
            return $transfer;
        }

        $this->encryptedTransfer = clone $transfer;
        $this->encryptedFields = new EncryptedFieldsBitSet();
        $fields = Extension::ENCRYPTED_TRANSFER_FIELDS;
        \array_walk($fields, function (string $field): void {
            $this->encryptField($field);
        });
        $this->encryptedTransfer->setEncryptedFields($this->encryptedFields);

        return $this->encryptedTransfer;
    }

    private function encryptField(string $field): void
    {
        $value = $this->encryptedTransfer->{'get' . \ucfirst($field)}();
        if ($value === '') {
            return;
        }

        try {
            $encryptedValue = $this->cryptService->encrypt($value);
            $this->encryptedTransfer->{'set' . \ucfirst($field)}($encryptedValue);
            $this->setFieldAsEncrypted($field);
        } catch (CryptException $e) {
            $this->logger->warning(
                \sprintf(
                    'Field "%s" in transfer DTO cannot be encrypted, it will be stored unencrypted, reason: %s',
                    $field,
                    $e->getMessage(),
                ),
            );
        }
    }

    private function setFieldAsEncrypted(string $field): void
    {
        $this->encryptedFields->set(
            \constant(\sprintf('%s::%s', EncryptedFieldsBitSet::class, \strtoupper($field))),
        );
    }
}
