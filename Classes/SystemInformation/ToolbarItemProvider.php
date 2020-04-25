<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\SystemInformation;

use Brotkrueml\JobRouterProcess\Extension;
use TYPO3\CMS\Backend\Backend\ToolbarItems\SystemInformationToolbarItem;
use TYPO3\CMS\Backend\Toolbar\Enumeration\InformationStatus;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
final class ToolbarItemProvider
{
    /** @var array|null */
    private $lastRunInformation;

    public function __construct()
    {
        $this->lastRunInformation = GeneralUtility::makeInstance(Registry::class)
            ->get(Extension::REGISTRY_NAMESPACE, 'startCommand.lastRun');
    }

    public function getItem(SystemInformationToolbarItem $systemInformationToolbarItem): void
    {
        $systemInformationToolbarItem->addSystemInformation(
            $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':startCommand.lastRunLabel'),
            $this->getMessage(),
            'jobrouter-process-toolbar',
            $this->getSeverity()
        );
    }

    protected function getMessage(): string
    {
        $languageService = $this->getLanguageService();

        if ($this->lastRunInformation === null) {
            return $languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':startCommand.neverRun');
        }

        if ($this->isWarning()) {
            $status = $languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.warning');
        } elseif ($this->isOverdue()) {
            $status = $languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.overdue');
        } else {
            $status = $languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.success');
        }

        return \sprintf(
            $languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':startCommand.lastRunMessage'),
            \date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'], $this->lastRunInformation['start']),
            \date($GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'], $this->lastRunInformation['start']),
            $status
        );
    }

    private function isWarning(): bool
    {
        return $this->lastRunInformation['exitCode'] > 0;
    }

    private function isOverdue(): bool
    {
        return $this->lastRunInformation['start'] < time() - 86400;
    }

    private function getSeverity(): string
    {
        if ($this->lastRunInformation === null) {
            return InformationStatus::STATUS_WARNING;
        }

        if ($this->isWarning() || $this->isOverdue()) {
            $severity = InformationStatus::STATUS_WARNING;
        } else {
            $severity = InformationStatus::STATUS_OK;
        }

        return $severity;
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
