<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Dto;

use Brotkrueml\JobRouterProcess\Crypt\Transfer\EncryptedFieldsBitSet;
use Brotkrueml\JobRouterProcess\Domain\Entity\Transfer as TransferEntity;

final class Transfer
{
    // The user interaction on the website has priority over eventually wrong
    // step parameters (and then error when starting the incident). So, for
    // avoiding exception on inserting to long data into the transfer table
    // the according parameter is cut to the maximum character length allowed.
    private const MAX_LENGTH_INITIATOR = 50;
    private const MAX_LENGTH_JOBFUNCTION = 50;
    private const MAX_LENGTH_SUMMARY = 255;
    private const MAX_LENGTH_USERNAME = 50;

    private string $type = '';
    private string $initiator = '';
    private string $username = '';
    private string $jobfunction = '';
    private string $summary = '';
    private int $priority = 2;
    private int $pool = 1;
    private string $processtable = '';
    private EncryptedFieldsBitSet $encryptedFields;

    public function __construct(
        private readonly int $crdate,
        private readonly int $stepUid,
        private readonly string $correlationId,
    ) {
        $this->encryptedFields = new EncryptedFieldsBitSet(0);
    }

    public function getCrdate(): int
    {
        return $this->crdate;
    }

    public function getStepUid(): int
    {
        return $this->stepUid;
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getInitiator(): string
    {
        return $this->initiator;
    }

    public function setInitiator(string $initiator): void
    {
        $this->initiator = $initiator;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getJobfunction(): string
    {
        return $this->jobfunction;
    }

    public function setJobfunction(string $jobfunction): void
    {
        $this->jobfunction = $jobfunction;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getPool(): int
    {
        return $this->pool;
    }

    public function setPool(int $pool): void
    {
        $this->pool = $pool;
    }

    public function getProcesstable(): string
    {
        return $this->processtable;
    }

    public function setProcesstable(string $processtable): void
    {
        $this->processtable = $processtable;
    }

    public function getEncryptedFields(): EncryptedFieldsBitSet
    {
        return $this->encryptedFields;
    }

    public function setEncryptedFields(EncryptedFieldsBitSet $encryptedFields): void
    {
        $this->encryptedFields = $encryptedFields;
    }

    /**
     * @internal
     */
    public static function fromEntity(TransferEntity $entity): self
    {
        $dto = new self(
            $entity->crdate,
            $entity->stepUid,
            $entity->correlationId,
        );

        $dto->setType($entity->type);
        $dto->setInitiator($entity->initiator);
        $dto->setUsername($entity->username);
        $dto->setJobfunction($entity->jobfunction);
        $dto->setSummary($entity->summary);
        $dto->setPriority($entity->priority);
        $dto->setPool($entity->pool);
        $dto->setProcesstable($entity->processtable);
        $dto->setEncryptedFields($entity->encryptedFields);

        return $dto;
    }

    /**
     * @return array<string, mixed>
     * @internal
     */
    public function toArray(): array
    {
        // @phpstan-ignore-next-line Array with keys is not allowed. Use value object to pass data instead
        return [
            'crdate' => $this->crdate,
            'step_uid' => $this->stepUid,
            'correlation_id' => $this->correlationId,
            'type' => $this->type,
            'initiator' => \mb_substr($this->initiator, 0, self::MAX_LENGTH_INITIATOR),
            'username' => \mb_substr($this->username, 0, self::MAX_LENGTH_USERNAME),
            'jobfunction' => \mb_substr($this->jobfunction, 0, self::MAX_LENGTH_JOBFUNCTION),
            'summary' => \mb_substr($this->summary, 0, self::MAX_LENGTH_SUMMARY),
            'priority' => $this->priority,
            'pool' => $this->pool,
            'processtable' => $this->processtable,
            'encrypted_fields' => $this->encryptedFields->__toInt(),
        ];
    }
}
