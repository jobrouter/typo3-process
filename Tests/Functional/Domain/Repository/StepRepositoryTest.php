<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Functional\Domain\Repository;

use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
use Brotkrueml\JobRouterProcess\Exception\StepNotFoundException;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class StepRepositoryTest extends FunctionalTestCase
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

    private StepRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new StepRepository($this->getConnectionPool());
    }

    #[Test]
    public function findAll(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Steps.csv');

        $actual = $this->subject->findAll();

        self::assertCount(2, $actual);
        self::assertSame(2, $actual[0]->uid);
        self::assertSame(3, $actual[1]->uid);
    }

    #[Test]
    public function findAllWithDisabled(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Steps.csv');

        $actual = $this->subject->findAll(true);

        self::assertCount(3, $actual);
        self::assertSame(2, $actual[0]->uid);
        self::assertSame(3, $actual[1]->uid);
        self::assertSame(1, $actual[2]->uid);
    }

    #[Test]
    public function findByUidWithRecordFound(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Steps.csv');

        $actual = $this->subject->findByUid(2);

        self::assertSame(2, $actual->uid);
    }

    #[Test]
    public function findByUidThrowsExceptionIfRecordNotFound(): void
    {
        $this->expectException(StepNotFoundException::class);

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Steps.csv');

        $this->subject->findByUid(9999);
    }

    #[Test]
    public function findByHandleWithRecordFound(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Steps.csv');

        $actual = $this->subject->findByHandle('handle_2');

        self::assertSame(2, $actual->uid);
    }

    #[Test]
    public function findByHandleThrowsExceptionIfRecordNotFound(): void
    {
        $this->expectException(StepNotFoundException::class);

        $this->subject->findByHandle('non_existing');
    }

    #[Test]
    public function findByProcessUidWithoutDisabled(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Steps.csv');

        $actual = $this->subject->findByProcessUid(42);

        self::assertCount(1, $actual);
        self::assertSame(2, $actual[0]->uid);
    }

    #[Test]
    public function findByProcessUidWithDisabled(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Steps.csv');

        $actual = $this->subject->findByProcessUid(42, true);

        self::assertCount(2, $actual);
        self::assertSame(1, $actual[0]->uid);
        self::assertSame(2, $actual[1]->uid);
    }

    #[Test]
    public function findByProcessUidAndNoRecordsFound(): void
    {
        $actual = $this->subject->findByProcessUid(9999);

        self::assertSame([], $actual);
    }
}
