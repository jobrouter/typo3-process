<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Transfer;

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\ResourceFactory;

/**
 * @internal
 */
class AttachmentDeleter
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ResourceFactory $resourceFactory,
    ) {}

    /**
     * @param string $file File path with identifier like "1:/user_upload/form_cabd2b9af41347e100bd042d1df1d8eb2543d903/foo.pdf"
     */
    public function deleteFile(string $file): void
    {
        if ($file === '') {
            return;
        }

        try {
            $fileObject = $this->resourceFactory->getFileObjectFromCombinedIdentifier($file);
        } catch (\InvalidArgumentException) {
            $this->logger->notice(\sprintf('Path of file "%s" cannot be resolved.', $file));
            return;
        }

        $storageConfiguration = $fileObject->getStorage()->getConfiguration();
        if ($storageConfiguration['pathType'] === 'relative') {
            $absoluteFolderPath = Environment::getPublicPath() . '/' . $storageConfiguration['basePath'];
        } else {
            $absoluteFolderPath = $storageConfiguration['basePath'];
        }
        $absoluteFilePath = \rtrim((string)$absoluteFolderPath, '/') . $fileObject->getIdentifier();
        if (@\unlink($absoluteFilePath)) {
            $this->logger->info(\sprintf('File "%s" was deleted successfully.', $absoluteFilePath));
        } else {
            $this->logger->warning(\sprintf('The file "%s" could not be deleted.', $absoluteFilePath));
        }

        $parentFolder = \dirname($absoluteFilePath);
        $isParentFolderEmpty = (\is_countable(\glob($parentFolder . '/*')) ? \count(\glob($parentFolder . '/*')) : 0) === 0;
        if (! $isParentFolderEmpty) {
            $this->logger->notice(\sprintf('The folder "%s" is not empty, not deleting.', $parentFolder));
            return;
        }

        if (@\rmdir($parentFolder)) {
            $this->logger->info(\sprintf('Folder "%s" was deleted successfully.', $parentFolder));
        } else {
            $this->logger->warning(\sprintf('The folder "%s" could not be deleted.', $parentFolder));
        }
    }
}
