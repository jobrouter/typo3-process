<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\EventListener;

use Brotkrueml\JobRouterProcess\Extension;
use TYPO3\CMS\Backend\Backend\Event\SystemInformationToolbarCollectorEvent;
use TYPO3\CMS\Backend\Toolbar\Enumeration\InformationStatus;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Registry;

/**
 * @internal
 */
final class ToolbarItemProvider
{
    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var array{exitCode?: int, start?: int}
     */
    private $lastRunInformation = [];

    public function __construct(LanguageService $languageService, Registry $registry)
    {
        $this->languageService = $languageService;
        $this->registry = $registry;
    }

    public function __invoke(SystemInformationToolbarCollectorEvent $event): void
    {
        $this->lastRunInformation = $this->registry->get(Extension::REGISTRY_NAMESPACE, 'startCommand.lastRun', []);

        $event->getToolbarItem()->addSystemInformation(
            $this->languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':startCommand.lastRunLabel'),
            $this->getMessage(),
            'jobrouter-process-toolbar',
            $this->getSeverity()
        );
    }

    private function getMessage(): string
    {
        if ($this->lastRunInformation === []) {
            return $this->languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':toolbar.neverExecuted');
        }

        if ($this->isWarning()) {
            $status = $this->languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.warning');
        } elseif ($this->isOverdue()) {
            $status = $this->languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.overdue');
        } else {
            $status = $this->languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':status.success');
        }

        return \sprintf(
            $this->languageService->sL(Extension::LANGUAGE_PATH_TOOLBAR . ':startCommand.lastRunMessage'),
            \date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'], $this->lastRunInformation['start']),
            \date($GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'], $this->lastRunInformation['start']),
            $status
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

    private function getSeverity(): string
    {
        if ($this->lastRunInformation === []) {
            return InformationStatus::STATUS_WARNING;
        }
        if ($this->isWarning()) {
            return InformationStatus::STATUS_WARNING;
        }
        if ($this->isOverdue()) {
            return InformationStatus::STATUS_WARNING;
        }

        return InformationStatus::STATUS_OK;
    }
}
