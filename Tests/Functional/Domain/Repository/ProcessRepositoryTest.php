<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Functional\Domain\Repository;

use Brotkrueml\JobRouterProcess\Domain\Repository\ProcessRepository;
use Brotkrueml\JobRouterProcess\Exception\ProcessNotFoundException;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ProcessRepositoryTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected array $coreExtensionsToLoad = [
        'form',
    ];

    /**
     * @var string[]
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_base',
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_process',
    ];

    private ProcessRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ProcessRepository($this->getConnectionPool());
    }

    /**
     * @test
     */
    public function findAll(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Processes.csv');

        $actual = $this->subject->findAll();

        self::assertCount(2, $actual);
        self::assertSame(2, $actual[0]->uid);
        self::assertSame(3, $actual[1]->uid);
    }

    /**
     * @test
     */
    public function findAllWithDisabled(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Processes.csv');

        $actual = $this->subject->findAll(true);

        self::assertCount(3, $actual);
        self::assertSame(2, $actual[0]->uid);
        self::assertSame(3, $actual[1]->uid);
        self::assertSame(1, $actual[2]->uid);
    }

    /**
     * @test
     */
    public function findByUidWithRecordFound(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Processes.csv');

        $actual = $this->subject->findByUid(3);

        self::assertSame(3, $actual->uid);
    }

    /**
     * @test
     */
    public function findByUidWithDisabled(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Processes.csv');

        $actual = $this->subject->findByUid(1, true);

        self::assertSame(1, $actual->uid);
    }

    /**
     * @test
     */
    public function findByUidWithDisabledRecordNotFoundAndThrowsException(): void
    {
        $this->expectException(ProcessNotFoundException::class);

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Processes.csv');

        $this->subject->findByUid(1);
    }

    /**
     * @test
     */
    public function findByUidWithNotAvailableRecordThrowsException(): void
    {
        $this->expectException(ProcessNotFoundException::class);

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Processes.csv');

        $this->subject->findByUid(9999);
    }
}
