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
use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Extension;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class Encrypter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var Crypt */
    private $cryptService;

    /** @var ExtensionConfiguration */
    private $extensionConfiguration;

    /** @var Transfer */
    private $encryptedTransfer;

    /** @var EncryptedFieldsBitSet */
    private $encryptedFields;

    public function __construct(Crypt $cryptService, ExtensionConfiguration $extensionConfiguration)
    {
        $this->cryptService = $cryptService;
        $this->extensionConfiguration = $extensionConfiguration;
    }

    public function encryptIfConfigured(Transfer $transfer): Transfer
    {
        if (!$this->extensionConfiguration->get(Extension::KEY, Extension::ENCRYPT_DATA_CONFIG_IDENTIFIER)) {
            return $transfer;
        }

        $this->encryptedTransfer = clone $transfer;
        $this->encryptedFields = new EncryptedFieldsBitSet();
        $fields = Extension::ENCRYPTED_TRANSFER_FIELDS;
        \array_walk($fields, [$this, 'encryptField']);
        $this->encryptedTransfer->setEncryptedFields($this->encryptedFields->__toInt());

        return $this->encryptedTransfer;
    }

    private function encryptField(string $field): void
    {
        $value = $this->encryptedTransfer->{'get' . \ucfirst($field)}();
        if (empty($value)) {
            return;
        }

        try {
            $encryptedValue = $this->cryptService->encrypt($value);
            $this->encryptedTransfer->{'set' . \ucfirst($field)}($encryptedValue);
            $this->setFieldAsEncrypted($field);
        } catch (CryptException $e) {
            $this->logger->warning(
                \sprintf(
                    'Field "%s" in transfer with identifier "%s" cannot be encrypted, it will be stored unencrypted, reason: %s',
                    $field,
                    $this->encryptedTransfer->getIdentifier(),
                    $e->getMessage()
                )
            );
        }
    }

    private function setFieldAsEncrypted(string $field): void
    {
        $this->encryptedFields->set(
            \constant(\sprintf('%s::%s', EncryptedFieldsBitSet::class, \strtoupper($field)))
        );
    }
}
