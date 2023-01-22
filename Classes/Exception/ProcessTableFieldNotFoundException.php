<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Exception;

final class ProcessTableFieldNotFoundException extends \RuntimeException
{
    public static function forField(string $processField, string $processName): self
    {
        return new self(
            \sprintf(
                'Process table field "%s" is not configured in process link "%s".',
                $processField,
                $processName,
            ),
            1582053551,
        );
    }
}
