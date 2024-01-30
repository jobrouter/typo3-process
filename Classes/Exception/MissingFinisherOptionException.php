<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Exception;

final class MissingFinisherOptionException extends \RuntimeException
{
    public static function forStepWithFormIdentifier(string $formIdentifier): self
    {
        return new self(
            \sprintf(
                'Step handle of form with identifier "%s" is not defined.',
                $formIdentifier,
            ),
            1581270462,
        );
    }
}
