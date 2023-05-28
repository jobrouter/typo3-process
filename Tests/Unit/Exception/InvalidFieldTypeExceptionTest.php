<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Exception;

use Brotkrueml\JobRouterBase\Enumeration\FieldType;
use Brotkrueml\JobRouterProcess\Exception\InvalidFieldTypeException;
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
