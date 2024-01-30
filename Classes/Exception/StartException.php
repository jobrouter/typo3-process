<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Exception;

final class StartException extends \RuntimeException
{
    public static function forUnavailableConnection(string $processName): self
    {
        return new self(
            \sprintf(
                'The connection for process with name "%s" is not available.',
                $processName,
            ),
            1674845516,
        );
    }
}
