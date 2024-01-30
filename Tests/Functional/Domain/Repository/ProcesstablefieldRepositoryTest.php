<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Functional\Domain\Repository;

use JobRouter\AddOn\Typo3Process\Domain\Repository\ProcessTableFieldRepository;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ProcesstablefieldRepositoryTest extends FunctionalTestCase
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

    private ProcessTableFieldRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ProcessTableFieldRepository($this->getConnectionPool());
    }

    #[Test]
    public function findByProcessUid(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Processtablefields.csv');

        $actual = $this->subject->findByProcessUid(1);

        self::assertCount(3, $actual);
        self::assertSame(2, $actual[0]->uid);
        self::assertSame(3, $actual[1]->uid);
        self::assertSame(1, $actual[2]->uid);
    }
}
