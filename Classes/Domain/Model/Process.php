<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Domain\Model;

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\JobRouterConnector\Domain\Model\Connection;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Process extends AbstractEntity
{
    /** @var string */
    protected $name = '';

    /** @var string */
    protected $description = '';

    /** @var \Brotkrueml\JobRouterConnector\Domain\Model\Connection|null */
    protected $connection;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Brotkrueml\JobRouterProcess\Domain\Model\Processtablefield>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $processtablefields;

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

    public function getProcesstablefields(): ObjectStorage
    {
        return $this->processtablefields;
    }

    public function setProcesstablefields(ObjectStorage $processtablefields)
    {
        $this->processtablefields = $processtablefields;
    }
}
