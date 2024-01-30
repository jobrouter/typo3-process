<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Exception;

final class PrepareException extends \RuntimeException
{
    public static function forNotWritable(): self
    {
        return new self(
            'Transfer record cannot be written, see log file for details.',
            1581278897,
        );
    }
}
