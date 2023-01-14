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
    // The user interaction on the website has priority over eventually wrong
    // step parameters (and then error when starting the incident). So, for
    // avoiding exception on inserting to long data into the transfer table
    // the according parameter is cut to the maximum character length allowed.
    protected const MAX_LENGTH_INITIATOR = 50;
    protected const MAX_LENGTH_JOBFUNCTION = 50;
    protected const MAX_LENGTH_SUMMARY = 255;
    protected const MAX_LENGTH_USERNAME = 50;

    /**
     * @var int
     */
    protected $crdate = 0;

    /**
     * @var int
     */
    protected $stepUid = 0;

    protected string $correlationId = '';

    protected string $type = '';

    protected string $initiator = '';

    protected string $username = '';

    protected string $jobfunction = '';

    protected string $summary = '';

    /**
     * @var int
     */
    protected $priority = 2;

    /**
     * @var int
     */
    protected $pool = 1;

    protected string $processtable = '';

    /**
     * @see class Brotkrueml\JobRouterProcess\Crypt\Transfer\EncryptedFieldsBitSet
     * @var int
     */
    protected $encryptedFields = 0;

    /**
     * @var bool
     */
    protected $startSuccess = false;

    protected ?\DateTime $startDate = null;

    protected string $startMessage = '';

    public function __construct()
    {
        $this->setPid(0);
    }

    public function getCrdate(): int
    {
        return $this->crdate;
    }

    public function setCrdate(int $crdate): void
    {
        $this->crdate = $crdate;
    }

    public function getStepUid(): int
    {
        return $this->stepUid;
    }

    public function setStepUid(int $stepUid): void
    {
        $this->stepUid = $stepUid;
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    public function setCorrelationId(string $correlationId): void
    {
        $this->correlationId = $correlationId;
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
        $this->initiator = \mb_substr($initiator, 0, self::MAX_LENGTH_INITIATOR);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = \mb_substr($username, 0, self::MAX_LENGTH_USERNAME);
    }

    public function getJobfunction(): string
    {
        return $this->jobfunction;
    }

    public function setJobfunction(string $jobfunction): void
    {
        $this->jobfunction = \mb_substr($jobfunction, 0, self::MAX_LENGTH_JOBFUNCTION);
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = \mb_substr($summary, 0, self::MAX_LENGTH_SUMMARY);
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(string|int $priority): void
    {
        if (! \is_numeric($priority)) {
            throw new \InvalidArgumentException(
                \sprintf('Priority has to be a number, "%s" given', $priority),
                1581703249,
            );
        }

        $priority = (int)$priority;

        if ($priority < 1 || $priority > 3) {
            throw new \InvalidArgumentException(
                \sprintf('Priority has to be between 1 and 3, "%d" given', $priority),
                1581282986,
            );
        }

        $this->priority = $priority;
    }

    public function getPool(): int
    {
        return $this->pool;
    }

    public function setPool(string|int $pool): void
    {
        if (! \is_numeric($pool)) {
            throw new \InvalidArgumentException(
                \sprintf('Pool has to be a number, "%s" given', $pool),
                1581703327,
            );
        }

        $pool = (int)$pool;

        if ($pool < 1) {
            throw new \InvalidArgumentException(
                \sprintf('Pool has to be a positive integer, "%d" given', $pool),
                1581283150,
            );
        }

        $this->pool = $pool;
    }

    public function getProcesstable(): string
    {
        return $this->processtable;
    }

    /**
     * @param array<string, mixed>|string $processtable
     * @throws \InvalidArgumentException
     * @throws \JsonException
     */
    public function setProcesstable(array|string $processtable): void
    {
        if (\is_array($processtable)) {
            $this->processtable = \json_encode($processtable, \JSON_THROW_ON_ERROR);
            return;
        }

        if (\is_string($processtable)) {
            $this->processtable = $processtable;
            return;
        }

        throw new \InvalidArgumentException(
            \sprintf(
                'Argument "processtable" must be either a string or an array, "%s" given',
                \gettype($processtable),
            ),
            1582744898,
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
