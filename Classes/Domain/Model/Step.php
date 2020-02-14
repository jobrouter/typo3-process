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

class Step extends AbstractEntity implements CommonStepParameterInterface
{
    use CommonStepParameterTrait;

    /** @var string */
    protected $handle = '';

    /** @var string */
    protected $name = '';

    /** @var \Brotkrueml\JobRouterProcess\Domain\Model\Process|null */
    protected $process;

    /** @var int */
    protected $stepNumber = 0;

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
}
