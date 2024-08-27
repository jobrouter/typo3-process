<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Functional\Transfer;

use JobRouter\AddOn\Typo3Connector\Service\Crypt;
use JobRouter\AddOn\Typo3Connector\Service\FileService;
use JobRouter\AddOn\Typo3Process\Domain\Repository\ProcessTableFieldRepository;
use JobRouter\AddOn\Typo3Process\Domain\Repository\TransferRepository;
use JobRouter\AddOn\Typo3Process\Transfer\AttachmentDeleter;
use JobRouter\AddOn\Typo3Process\Transfer\Deleter;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class DeleterTest extends FunctionalTestCase
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

    private Deleter $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $attachmentDeleter = new AttachmentDeleter(new NullLogger(), $this->getContainer()->get(ResourceFactory::class));
        $processTableFieldRepository = new ProcessTableFieldRepository($this->getConnectionPool());
        $transferRepository = new TransferRepository($this->getConnectionPool());

        $this->subject = new Deleter(
            $attachmentDeleter,
            new Crypt(new FileService($this->createStub(ExtensionConfiguration::class))),
            new NullLogger(),
            $processTableFieldRepository,
            $transferRepository,
        );
    }

    #[Test]
    public function runReturns0WhenNoTransfersAreAvailable(): void
    {
        $actual = $this->subject->run(30);

        self::assertSame(0, $actual);
    }

    #[Test]
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
