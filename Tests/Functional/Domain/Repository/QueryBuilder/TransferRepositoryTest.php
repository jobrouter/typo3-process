<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Functional\Domain\Repository\QueryBuilder;

use Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder\TransferRepository;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class TransferRepositoryTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_process',
    ];
    private TransferRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new TransferRepository($this->getConnectionPool());
    }

    /**
     * @test
     */
    public function findForDeletionReturnsRecordsCorrectly(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/TransferFindForDeletion.csv');

        $actual = $this->subject->findForDeletion(1666666667);

        self::assertCount(3, $actual);
        self::assertSame(101, (int)$actual[0]['uid']);
        self::assertSame(11, (int)$actual[0]['process_uid']);
        self::assertSame(102, (int)$actual[1]['uid']);
        self::assertNull($actual[1]['process_uid']);
        self::assertSame(103, (int)$actual[2]['uid']);
        self::assertNull($actual[2]['process_uid']);
    }

    /**
     * @test
     */
    public function deleteDeletesGivenIdIfStartWasSuccessful(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/TransferDelete.csv');

        $actual = $this->subject->delete(11);

        self::assertSame(1, $actual);

        $connection = $this->getConnectionPool()->getConnectionForTable('tx_jobrouterprocess_domain_model_transfer');
        $count = $connection->count(
            '*',
            'tx_jobrouterprocess_domain_model_transfer',
            [
                'uid' => 11,
            ],
        );

        self::assertSame(0, $count);
    }

    /**
     * @test
     */
    public function deleteDoesNotDeleteGivenIdIfStartWasUnsuccessful(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/TransferDelete.csv');

        $actual = $this->subject->delete(22);

        self::assertSame(0, $actual);

        $connection = $this->getConnectionPool()->getConnectionForTable('tx_jobrouterprocess_domain_model_transfer');
        $count = $connection->count(
            '*',
            'tx_jobrouterprocess_domain_model_transfer',
            [
                'uid' => 22,
            ],
        );

        self::assertSame(1, $count);
    }
}
