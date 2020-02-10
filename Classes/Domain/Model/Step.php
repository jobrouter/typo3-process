<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Domain\Model;

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Step extends AbstractEntity
{
    /** @var string */
    protected $handle = '';

    /** @var string */
    protected $name = '';

    /** @var \Brotkrueml\JobRouterProcess\Domain\Model\Process|null */
    protected $process;

    /** @var int */
    protected $stepNumber = 0;

    /** @var string */
    protected $initiator = '';

    /** @var string */
    protected $username = '';

    /** @var string */
    protected $jobfunction = '';

    /** @var string */
    protected $summary = '';

    /** @var int */
    protected $priority = 2;

    /** @var int */
    protected $pool = 1;

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function setHandle(string $handle): void
    {
        $this->handle = $handle;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getProcess(): ?Process
    {
        return $this->process;
    }

    public function setProcess(Process $process): void
    {
        $this->process = $process;
    }

    public function getStepNumber(): int
    {
        return $this->stepNumber;
    }

    public function setStepNumber(int $stepNumber): void
    {
        if ($stepNumber < 1) {
            throw new \InvalidArgumentException(
                \sprintf('Step number has to be a positive integer, "%d" given', $stepNumber),
                1581282590
            );
        }

        $this->stepNumber = $stepNumber;
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

    public function setPool(int $pool): void
    {
        if ($pool < 1) {
            throw new \InvalidArgumentException(
                \sprintf('Pool has to be a positive integer, "%d" given', $pool),
                1581283150
            );
        }

        $this->pool = $pool;
    }

    public function getDefaultParameters(): array
    {
        $parameters = [];

        if (!empty($this->initiator)) {
            $parameters['initiator'] = $this->initiator;
        }

        if (!empty($this->username)) {
            $parameters['username'] = $this->username;
        }

        if (!empty($this->jobfunction)) {
            $parameters['jobfunction'] = $this->jobfunction;
        }

        if (!empty($this->summary)) {
            $parameters['summary'] = $this->summary;
        }

        if ($this->priority !== 2) {
            $parameters['priority'] = $this->priority;
        }

        if ($this->pool > 1) {
            $parameters['pool'] = $this->pool;
        }

        return $parameters;
    }
}
