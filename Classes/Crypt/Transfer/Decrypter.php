<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Crypt\Transfer;

use Brotkrueml\JobRouterConnector\Exception\CryptException;
use Brotkrueml\JobRouterConnector\Service\Crypt;
use Brotkrueml\JobRouterProcess\Domain\Dto\Transfer;
use Brotkrueml\JobRouterProcess\Exception\DecryptException;
use Brotkrueml\JobRouterProcess\Extension;

class Decrypter
{
    private Transfer $decryptedTransfer;
    private EncryptedFieldsBitSet $encryptedFields;

    public function __construct(
        private readonly Crypt $cryptService,
    ) {
    }

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
            throw new DecryptException(
                \sprintf(
                    'Field "%s" in transfer cannot be decrypted, reason: %s',
                    $field,
                    $e->getMessage(),
                ),
                1599323431,
                $e,
            );
        }
    }

    private function isFieldEncrypted(string $field): bool
    {
        return $this->encryptedFields->get(
            \constant(\sprintf('%s::%s', EncryptedFieldsBitSet::class, \strtoupper($field))),
        );
    }
}
