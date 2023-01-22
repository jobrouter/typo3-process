<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Exception;

final class MissingProcessTableFieldException extends \RuntimeException
{
    public static function forField(string $processField, string $processName, string $formIdentifier): self
    {
        return new self(
            \sprintf(
                'Process table field "%s" is used in form with identifier "%s", but not defined in process link "%s".',
                $processField,
                $formIdentifier,
                $processName,
            ),
            1585930166,
        );
    }
}
