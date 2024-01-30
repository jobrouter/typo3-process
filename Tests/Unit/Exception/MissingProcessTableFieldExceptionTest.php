<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Unit\Exception;

use JobRouter\AddOn\Typo3Process\Exception\MissingProcessTableFieldException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MissingProcessTableFieldExceptionTest extends TestCase
{
    #[Test]
    public function forField(): void
    {
        $actual = MissingProcessTableFieldException::forField(
            'someProcessField',
            42,
            'someFormIdentifier',
        );

        self::assertSame('Process table field "someProcessField" is used in form with identifier "someFormIdentifier", but not defined in process uid "42".', $actual->getMessage());
        self::assertSame(1585930166, $actual->getCode());
    }
}
