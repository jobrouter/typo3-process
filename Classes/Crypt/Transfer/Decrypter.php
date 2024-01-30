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
use JobRouter\AddOn\Typo3Process\Exception\DecryptException;
use JobRouter\AddOn\Typo3Process\Extension;

class Decrypter
{
    private Transfer $decryptedTransfer;
    private EncryptedFieldsBitSet $encryptedFields;

    public function __construct(
        private readonly Crypt $cryptService,
    ) {}

    public function decryptIfEncrypted(Transfer $transfer): Transfer
    {
        if ($transfer->getEncryptedFields()->__toInt() === 0) {
            return $transfer;
        }

        $this->decryptedTransfer = clone $transfer;
        $this->encryptedFields = $transfer->getEncryptedFields();
        $fields = Extension::ENCRYPTED_TRANSFER_FIELDS;
        foreach ($fields as $field) {
            $this->decryptField($field);
        }
        $this->decryptedTransfer->setEncryptedFields(new EncryptedFieldsBitSet(0));

        return $this->decryptedTransfer;
    }

    private function decryptField(string $field): void
    {
        if (! $this->isFieldEncrypted($field)) {
            return;
        }

        try {
            $decryptedValue = $this->cryptService->decrypt($this->decryptedTransfer->{'get' . \ucfirst($field)}());
            $this->decryptedTransfer->{'set' . \ucfirst($field)}($decryptedValue);
        } catch (CryptException $e) {
            throw DecryptException::forField($field, $e);
        }
    }

    private function isFieldEncrypted(string $field): bool
    {
        return $this->encryptedFields->get(
            \constant(\sprintf('%s::%s', EncryptedFieldsBitSet::class, \strtoupper($field))),
        );
    }
}
