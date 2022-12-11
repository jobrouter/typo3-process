<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Functional\Transfer;

use Brotkrueml\JobRouterConnector\Service\Crypt;
use Brotkrueml\JobRouterProcess\Domain\Repository\ProcessRepository;
use Brotkrueml\JobRouterProcess\Domain\Repository\QueryBuilder\TransferRepository;
use Brotkrueml\JobRouterProcess\Transfer\AttachmentDeleter;
use Brotkrueml\JobRouterProcess\Transfer\Deleter;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class DeleterTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_process',
    ];

    private Deleter $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $attachmentDeleter = new AttachmentDeleter(GeneralUtility::makeInstance(ResourceFactory::class));
        $crypt = new Crypt();
        $processRepository = GeneralUtility::makeInstance(ProcessRepository::class);
        $transferRepository = new TransferRepository($this->getConnectionPool());

        $this->subject = new Deleter($attachmentDeleter, $crypt, $processRepository, $transferRepository);
        $this->subject->setLogger(new NullLogger());
    }

    /**
     * @test
     */
    public function runReturns0WhenNoTransfersAreAvailable(): void
    {
        $actual = $this->subject->run(30);

        self::assertSame(0, $actual);
    }

    /**
     * @test
     */
    public function runReturnsNumberOfSuccessfulDeletedTransfers(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Deleter.csv');

        $actual = $this->subject->run(30);

        self::assertSame(2, $actual);

        $connection = $this->getConnectionPool()->getConnectionForTable('tx_jobrouterprocess_domain_model_transfer');
        $actual = $connection->count('*', 'tx_jobrouterprocess_domain_model_transfer', [
            'uid' => 11,
        ]);
        self::assertSame(0, $actual);
        $actual = $connection->count('*', 'tx_jobrouterprocess_domain_model_transfer', [
            'uid' => 12,
        ]);
        self::assertSame(0, $actual);
    }
}