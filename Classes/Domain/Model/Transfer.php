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

class Transfer extends AbstractEntity implements CommonStepParameterInterface
{
    use CommonStepParameterTrait;

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
    protected $processtable = '';

    /**
     * @var bool
     */
    protected $transmitSuccess = false;

    /**
     * @var \DateTime
     */
    protected $transmitDate;

    /**
     * @var string
     */
    protected $transmitMessage = '';

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

    public function getProcesstable(): string
    {
        return $this->processtable;
    }

    public function setProcesstable(string $processtable): void
    {
        $this->processtable = $processtable;
    }

    public function isTransmitSuccess(): bool
    {
        return $this->transmitSuccess;
    }

    public function setTransmitSuccess(bool $transmitSuccess): void
    {
        $this->transmitSuccess = $transmitSuccess;
    }

    public function getTransmitDate(): ?\DateTime
    {
        return $this->transmitDate;
    }

    public function setTransmitDate(\DateTime $transmitDate): void
    {
        $this->transmitDate = $transmitDate;
    }

    public function getTransmitMessage(): string
    {
        return $this->transmitMessage;
    }

    public function setTransmitMessage(string $transmitMessage): void
    {
        $this->transmitMessage = $transmitMessage;
    }
}
