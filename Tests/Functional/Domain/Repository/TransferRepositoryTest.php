<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Functional\Domain\Repository;

use Brotkrueml\JobRouterProcess\Crypt\Transfer\EncryptedFieldsBitSet;
use Brotkrueml\JobRouterProcess\Domain\Dto\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class TransferRepositoryTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_base',
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
    public function findNotStartedWithNoEntriesInTransferTable(): void
    {
        $actual = $this->subject->findNotStarted();

        self::assertSame([], $actual);
    }

    /**
     * @test
     */
    public function findNotStarted(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Transfers.csv');

        $actual = $this->subject->findNotStarted();

        self::assertCount(4, $actual);
        self::assertSame(2, $actual[0]->uid);
        self::assertSame(3, $actual[1]->uid);
        self::assertSame(4, $actual[2]->uid);
        self::assertSame(6, $actual[3]->uid);
    }

    /**
     * @test
     */
    public function findErroneousWithNoEntriesInTransferTable(): void
    {
        $actual = $this->subject->findErroneous();

        self::assertSame([], $actual);
    }

    /**
     * @test
     */
    public function findErroneous(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Transfers.csv');

        $actual = $this->subject->findErroneous();

        self::assertCount(1, $actual);
        self::assertSame(2, $actual[0]->uid);
    }

    /**
     * @test
     */
    public function add(): void
    {
        $transfer = new Transfer(1234567890, 42, 'some correlation');
        $transfer->setType('some type');
        $transfer->setInitiator('some initiator');
        $transfer->setUsername('some username');
        $transfer->setJobfunction('some jobfunction');
        $transfer->setSummary('some summary');
        $transfer->setPriority(3);
        $transfer->setPool(2);
        $transfer->setProcesstable('{"some":"processtable"}');
        $transfer->setEncryptedFields(new EncryptedFieldsBitSet(2));

        $actual = $this->subject->add($transfer);

        self::assertSame(1, $actual);

        $row = $this->getConnectionPool()
            ->getConnectionForTable('tx_jobrouterprocess_domain_model_transfer')
            ->select(
                ['*'],
                'tx_jobrouterprocess_domain_model_transfer',
            )
            ->fetchAssociative();

        self::assertSame($transfer->getCrdate(), $row['crdate']);
        self::assertSame($transfer->getStepUid(), $row['step_uid']);
        self::assertSame($transfer->getCorrelationId(), $row['correlation_id']);
        self::assertSame($transfer->getType(), $row['type']);
        self::assertSame($transfer->getInitiator(), $row['initiator']);
        self::assertSame($transfer->getUsername(), $row['username']);
        self::assertSame($transfer->getJobfunction(), $row['jobfunction']);
        self::assertSame($transfer->getSummary(), $row['summary']);
        self::assertSame($transfer->getPriority(), $row['priority']);
        self::assertSame($transfer->getPool(), $row['pool']);
        self::assertSame($transfer->getProcesstable(), $row['processtable']);
        self::assertSame($transfer->getEncryptedFields()->__toInt(), $row['encrypted_fields']);
        self::assertSame(0, (int)$row['start_success']);
        self::assertSame(0, (int)$row['start_date']);
        self::assertSame('', (string)$row['start_message']);
    }

    /**
     * @test
     */
    public function updateStartFields(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Transfers.csv');

        $date = \time();
        $actual = $this->subject->updateStartFields(4, true, $date, 'some message');

        self::assertSame(1, $actual);

        $row = $this->getConnectionPool()
            ->getConnectionForTable('tx_jobrouterprocess_domain_model_transfer')
            ->select(
                ['*'],
                'tx_jobrouterprocess_domain_model_transfer',
                [
                    'uid' => 4,
                ],
            )
            ->fetchAssociative();

        self::assertSame(1, (int)$row['start_success']);
        self::assertSame($date, (int)$row['start_date']);
        self::assertSame('some message', $row['start_message']);
    }

    /**
     * @test
     */
    public function countGroupByStartSuccessWithNoEntriesInTransferTable(): void
    {
        $actual = $this->subject->countGroupByStartSuccess();

        self::assertSame([], $actual);
    }

    /**
     * @test
     */
    public function countGroupByStartSuccess(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Transfers.csv');

        $actual = $this->subject->countGroupByStartSuccess();

        self::assertCount(2, $actual);
        self::assertSame(0, $actual[0]['start_success']);
        self::assertSame(4, $actual[0]['count']);
        self::assertSame(1, $actual[1]['start_success']);
        self::assertSame(2, $actual[1]['count']);
    }

    /**
     * @test
     */
    public function countStartFailedWithNoEntriesInTransferTable(): void
    {
        $actual = $this->subject->countStartFailed();

        self::assertSame(0, $actual);
    }

    /**
     * @test
     */
    public function countStartFailed(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Transfers.csv');

        $actual = $this->subject->countStartFailed();

        self::assertSame(1, $actual);
    }

    /**
     * @test
     */
    public function countTypesWithNoEntriesInTransferTable(): void
    {
        $actual = $this->subject->countTypes(30);

        self::assertSame([], $actual);
    }

    /**
     * @test
     */
    public function countByDayWithNoEntriesInTransferTable(): void
    {
        $actual = $this->subject->countTypes(30);

        self::assertSame([], $actual);
    }

    /**
     * @test
     */
    public function findFirstCreationDateWithNoEntriesInTransferTable(): void
    {
        $actual = $this->subject->findFirstCreationDate();

        self::assertSame(0, $actual);
    }

    /**
     * @test
     */
    public function findFirstCreationDate(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/Transfers.csv');

        $actual = $this->subject->findFirstCreationDate();

        self::assertSame(1111111111, $actual);
    }

    /**
     * @test
     */
    public function findForDeletionWithNoEntriesInTransferTable(): void
    {
        $actual = $this->subject->findForDeletion(1111111111);

        self::assertSame([], $actual);
    }

    /**
     * @test
     */
    public function findForDeletion(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/TransferFindForDeletion.csv');

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
    public function deleteWithNonExistingUid(): void
    {
        $actual = $this->subject->delete(9999);

        self::assertSame(0, $actual);
    }

    /**
     * @test
     */
    public function deleteDeletesGivenIdIfStartWasSuccessful(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/TransferDelete.csv');

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
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/TransferDelete.csv');

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
