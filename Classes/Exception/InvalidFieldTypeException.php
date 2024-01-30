<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Exception;

use JobRouter\AddOn\Typo3Base\Enumeration\FieldType;

final class InvalidFieldTypeException extends \RuntimeException
{
    public static function forFieldType(FieldType $fieldType): self
    {
        return new self(
            \sprintf('The field type "%s" is invalid.', $fieldType->name),
            1581344823,
        );
    }
}
