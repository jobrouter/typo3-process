<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\EventListener;

use JobRouter\AddOn\Typo3Process\Extension;
use TYPO3\CMS\Backend\Backend\Event\SystemInformationToolbarCollectorEvent;
use TYPO3\CMS\Backend\Toolbar\Enumeration\InformationStatus as DeprecatedInformationStatus;
use TYPO3\CMS\Backend\Toolbar\InformationStatus;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Registry;

/**
 * @internal
 */
final class ToolbarItemProvider
{
    /**
     * @var array{exitCode?: int, start?: int}
     */
    private array $lastRunInformation = [];

    public function __construct(
        private readonly Registry $registry,
    ) {}

    public function __invoke(SystemInformationToolbarCollectorEvent $event): void
    {
        $this->lastRunInformation = $this->registry->get(Extension::REGISTRY_NAMESPACE, 'startCommand.lastRun', []);

        $event->getToolbarItem()->addSystemInformation(
            $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':startCommand.lastRunLabel'),
            $this->getMessage(),
            'jobrouter-process-toolbar',
            $this->getSeverity(),
        );
    }

    private function getMessage(): string
    {
        if ($this->lastRunInformation === []) {
            return $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':toolbar.neverExecuted');
        }

        if ($this->isWarning()) {
            $status = $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.warning');
        } elseif ($this->isOverdue()) {
            $status = $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.overdue');
        } else {
            $status = $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.success');
        }

        return \sprintf(
            $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':startCommand.lastRunMessage'),
            \date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'], $this->lastRunInformation['start'] ?? 0),
            \date($GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'], $this->lastRunInformation['start'] ?? 0),
            $status,
        );
    }

    private function isWarning(): bool
    {
        return ($this->lastRunInformation['exitCode'] ?? 0) > 0;
    }

    private function isOverdue(): bool
    {
        return ($this->lastRunInformation['start'] ?? 0) < \time() - 86400;
    }

    private function getSeverity(): string|InformationStatus
    {
        // @todo Remove switch when compatibility with TYPO3 v12 is dropped
        $isVersion12 = (new Typo3Version())->getMajorVersion() === 12;

        if ($this->lastRunInformation === []) {
            return $isVersion12 ? DeprecatedInformationStatus::STATUS_WARNING : InformationStatus::WARNING;
        }
        if ($this->isWarning()) {
            return $isVersion12 ? DeprecatedInformationStatus::STATUS_WARNING : InformationStatus::WARNING;
        }
        if ($this->isOverdue()) {
            return $isVersion12 ? DeprecatedInformationStatus::STATUS_WARNING : InformationStatus::WARNING;
        }

        return $isVersion12 ? DeprecatedInformationStatus::STATUS_OK : InformationStatus::OK;
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
