<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Domain\Model;

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterProcess\Enumeration\Priority;

trait CommonStepParameterTrait
{
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

        if ($this->priority !== Priority::NORMAL) {
            $parameters['priority'] = $this->priority;
        }

        if ($this->pool > 1) {
            $parameters['pool'] = $this->pool;
        }

        return $parameters;
    }
}
