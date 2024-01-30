<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Unit\Exception;

use JobRouter\AddOn\Typo3Base\Enumeration\FieldType;
use JobRouter\AddOn\Typo3Process\Exception\InvalidFieldTypeException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InvalidFieldTypeExceptionTest extends TestCase
{
    #[Test]
    public function forFieldType(): void
    {
        $actual = InvalidFieldTypeException::forFieldType(FieldType::Date);

        self::assertSame('The field type "Date" is invalid.', $actual->getMessage());
        self::assertSame(1581344823, $actual->getCode());
    }
}
