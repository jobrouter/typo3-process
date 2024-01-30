<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Functional\Transfer;

use JobRouter\AddOn\Typo3Process\Transfer\AttachmentDeleter;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class AttachmentDeleterTest extends FunctionalTestCase
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

    /**
     * @var string[]
     */
    protected array $additionalFoldersToCreate = [
        'fileadmin/user_upload',
    ];

    private AttachmentDeleter $subject;

    protected function setUp(): void
    {
        if ((new Typo3Version())->getMajorVersion() === 10) {
            self::markTestSkipped('Does not run in TYPO3 v10');
        }

        parent::setUp();
        $this->subject = new AttachmentDeleter(new NullLogger(), GeneralUtility::makeInstance(ResourceFactory::class));
    }

    #[Test]
    public function deleteFileDeletesExistingFileAndParentFolderCorrectly(): void
    {
        $attachmentFolder = $this->getRandomAttachmentFolder();
        $absoluteAttachmentFolder = $this->getInstancePath() . '/fileadmin/' . $attachmentFolder;
        \mkdir($absoluteAttachmentFolder);

        \touch($absoluteAttachmentFolder . '/some.txt');

        $filePath = '1:/' . $attachmentFolder . '/some.txt';

        $this->subject->deleteFile($filePath);

        self::assertFileDoesNotExist($absoluteAttachmentFolder . '/some.txt');
        self::assertDirectoryDoesNotExist($absoluteAttachmentFolder);
    }

    #[Test]
    public function deleteFileDeletedExistingFileButDoesNotDeleteParentFolderWhenAnotherFileExists(): void
    {
        $attachmentFolder = $this->getRandomAttachmentFolder();
        $absoluteAttachmentFolder = $this->getInstancePath() . '/fileadmin/' . $attachmentFolder;
        \mkdir($absoluteAttachmentFolder);

        \touch($absoluteAttachmentFolder . '/some.txt');
        \touch($absoluteAttachmentFolder . '/another.txt');

        $filePath = '1:/' . $attachmentFolder . '/some.txt';

        $this->subject->deleteFile($filePath);

        self::assertFileDoesNotExist($absoluteAttachmentFolder . '/some.txt');
        self::assertFileExists($absoluteAttachmentFolder . '/another.txt');
        self::assertDirectoryExists($absoluteAttachmentFolder);
    }

    #[Test]
    public function deleteFileDoesNothingWhenFileWithIdentifierCannotBeResolved(): void
    {
        $attachmentFolder = $this->getRandomAttachmentFolder();
        $absoluteAttachmentFolder = $this->getInstancePath() . '/fileadmin/' . $attachmentFolder;
        \mkdir($absoluteAttachmentFolder);

        \touch($absoluteAttachmentFolder . '/another.txt');

        $filePath = '1:/' . $attachmentFolder . '/some.txt';

        $this->subject->deleteFile($filePath);

        self::assertFileExists($absoluteAttachmentFolder . '/another.txt');
        self::assertDirectoryExists($absoluteAttachmentFolder);
    }

    private function getRandomAttachmentFolder(): string
    {
        return 'user_upload/' . \sha1(\microtime());
    }
}
