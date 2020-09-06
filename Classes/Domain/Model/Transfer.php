<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Transfer extends AbstractEntity
{
    /**
     * @var int
     */
    protected $stepUid = 0;

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $initiator = '';

    /**
     * @var string
     */
    protected $username = '';

    /**
     * @var string
     */
    protected $jobfunction = '';

    /**
     * @var string
     */
    protected $summary = '';

    /**
     * @var int
     */
    protected $priority = 2;

    /**
     * @var int
     */
    protected $pool = 1;

    /**
     * @var string
     */
    protected $processtable = '';

    /**
     * @see class Brotkrueml\JobRouterProcess\Crypt\Transfer\EncryptedFieldsBitSet
     * @var int
     */
    protected $encryptedFields = 0;

    /**
     * @var bool
     */
    protected $startSuccess = false;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var string
     */
    protected $startMessage = '';

    public function __construct()
    {
        $this->setPid(0);
    }

    public function getStepUid(): int
    {
        return $this->stepUid;
    }

    public function setStepUid(int $stepUid): void
    {
        $this->stepUid = $stepUid;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
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

    /**
     * @param string|int $priority
     */
    public function setPriority($priority): void
    {
        if (!\is_numeric($priority)) {
            throw new \InvalidArgumentException(
                \sprintf('Priority has to be a number, "%s" given', $priority),
                1581703249
            );
        }

        $priority = (int)$priority;

        if ($priority < 1 || $priority > 3) {
            throw new \InvalidArgumentException(
                \sprintf('Priority has to be between 1 and 3, "%d" given', $priority),
                1581282986
            );
        }

        $this->priority = $priority;
    }

    public function getPool(): int
    {
        return $this->pool;
    }

    /**
     * @param string|int $pool
     */
    public function setPool($pool): void
    {
        if (!\is_numeric($pool)) {
            throw new \InvalidArgumentException(
                \sprintf('Pool has to be a number, "%s" given', $pool),
                1581703327
            );
        }

        $pool = (int)$pool;

        if ($pool < 1) {
            throw new \InvalidArgumentException(
                \sprintf('Pool has to be a positive integer, "%d" given', $pool),
                1581283150
            );
        }

        $this->pool = $pool;
    }

    public function getProcesstable(): string
    {
        return $this->processtable;
    }

    /**
     * @param array|string $processtable
     * @throws \InvalidArgumentException
     */
    public function setProcesstable($processtable): void
    {
        if (\is_array($processtable)) {
            $this->processtable = \json_encode($processtable);
            return;
        }

        if (\is_string($processtable)) {
            $this->processtable = $processtable;
            return;
        }

        throw new \InvalidArgumentException(
            \sprintf(
                'Argument "processtable" must be either a string or an array, "%s" given',
                \gettype($processtable)
            ),
            1582744898
        );
    }

    public function getEncryptedFields(): int
    {
        return $this->encryptedFields;
    }

    public function setEncryptedFields(int $encryptedFields): void
    {
        $this->encryptedFields = $encryptedFields;
    }

    public function isStartSuccess(): bool
    {
        return $this->startSuccess;
    }

    public function setStartSuccess(bool $startSuccess): void
    {
        $this->startSuccess = $startSuccess;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getStartMessage(): string
    {
        return $this->startMessage;
    }

    public function setStartMessage(string $startMessage): void
    {
        $this->startMessage = $startMessage;
    }
}
