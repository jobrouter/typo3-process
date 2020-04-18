<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Model;

use Brotkrueml\JobRouterConnector\Domain\Model\Connection;
use Brotkrueml\JobRouterProcess\Domain\Model\Process;
use Brotkrueml\JobRouterProcess\Domain\Model\Processtablefield;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ProcessTest extends TestCase
{
    /** @var Process */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new Process();
    }

    /**
     * @test
     */
    public function getAndSetName(): void
    {
        self::assertSame('', $this->subject->getName());

        $this->subject->setName('some name');

        self::assertSame('some name', $this->subject->getName());
    }

    /**
     * @test
     */
    public function getAndSetDescription(): void
    {
        self::assertSame('', $this->subject->getDescription());

        $this->subject->setDescription('some description');

        self::assertSame('some description', $this->subject->getDescription());
    }

    /**
     * @test
     */
    public function getAndSetConnection(): void
    {
        self::assertNull($this->subject->getConnection());

        $connection = new Connection();
        $this->subject->setConnection($connection);

        self::assertSame($connection, $this->subject->getConnection());
    }

    /**
     * @test
     */
    public function getProcesstablefieldsReturnsAnEmptyObjectStorageInitially(): void
    {
        self::assertInstanceOf(ObjectStorage::class, $this->subject->getProcesstablefields());
        self::assertSame(0, $this->subject->getProcesstablefields()->count());
    }

    /**
     * @test
     */
    public function addRemoveAndGetProcesstablefields(): void
    {
        $field1 = new Processtablefield();
        $field1->setName('field 1');

        $field2 = new Processtablefield();
        $field2->setName('field 2');

        $this->subject->addProcesstablefield($field1);
        self::assertSame(1, $this->subject->getProcesstablefields()->count());

        $this->subject->addProcesstablefield($field2);
        self::assertSame(2, $this->subject->getProcesstablefields()->count());

        self::assertTrue($this->subject->getProcesstablefields()->contains($field1));
        self::assertTrue($this->subject->getProcesstablefields()->contains($field2));

        $this->subject->removeProcesstablefield($field1);
        self::assertSame(1, $this->subject->getProcesstablefields()->count());
        self::assertFalse($this->subject->getProcesstablefields()->contains($field1));
    }

    /**
     * @test
     */
    public function setProcesstablefields(): void
    {
        $field = new Processtablefield();
        $field->setName('field');

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($field);

        $this->subject->setProcesstablefields($objectStorage);

        self::assertSame($objectStorage, $this->subject->getProcesstablefields());
    }
}
