<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess;

/**
 * @internal
 */
final class Extension
{
    public const KEY = 'jobrouter_process';

    private const LANGUAGE_PATH = 'LLL:EXT:' . self::KEY . '/Resources/Private/Language/';
    public const LANGUAGE_PATH_BACKEND_MODULE = self::LANGUAGE_PATH . 'BackendModule.xlf';
    public const LANGUAGE_PATH_DASHBOARD = self::LANGUAGE_PATH . 'Dashboard.xlf';
    public const LANGUAGE_PATH_DATABASE = self::LANGUAGE_PATH . 'Database.xlf';
    public const LANGUAGE_PATH_TOOLBAR = self::LANGUAGE_PATH . 'Toolbar.xlf';

    public const REGISTRY_NAMESPACE = 'tx_' . self::KEY;

    public const WIDGET_DEFAULT_CHART_COLOUR = '#fabb00';
    public const WIDGET_TRANSFER_TYPE_DEFAULT_NUMBER_OF_DAYS = 14;

    public const ENCRYPT_DATA_CONFIG_IDENTIFIER = 'encryptTransferData';
    public const ENCRYPTED_TRANSFER_FIELDS = ['processtable', 'summary'];
}
