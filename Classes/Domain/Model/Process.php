<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Model;

use Brotkrueml\JobRouterConnector\Domain\Model\Connection;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Process extends AbstractEntity
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var \Brotkrueml\JobRouterConnector\Domain\Model\Connection|null
     */
    protected $connection;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Brotkrueml\JobRouterProcess\Domain\Model\Processtablefield>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $processtablefields;

    /**
     * @var bool
     */
    protected $disabled = false;

    public function __construct()
    {
        $this->initStorageObjects();
    }

    protected function initStorageObjects(): void
    {
        $this->processtablefields = new ObjectStorage();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getConnection(): ?Connection
    {
        return $this->connection;
    }

    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }

    public function addProcesstablefield(Processtablefield $processtablefield): void
    {
        $this->processtablefields->attach($processtablefield);
    }

    public function removeProcesstablefield(Processtablefield $processtablefieldToRemove): void
    {
        $this->processtablefields->detach($processtablefieldToRemove);
    }

    /**
     * @return ObjectStorage<Processtablefield>
     */
    public function getProcesstablefields(): ObjectStorage
    {
        return $this->processtablefields;
    }

    /**
     * @param ObjectStorage<Processtablefield> $processtablefields
     */
    public function setProcesstablefields(ObjectStorage $processtablefields): void
    {
        $this->processtablefields = $processtablefields;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }
}
