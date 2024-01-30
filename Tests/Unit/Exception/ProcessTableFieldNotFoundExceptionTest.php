<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Unit\Exception;

use JobRouter\AddOn\Typo3Process\Exception\ProcessTableFieldNotFoundException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProcessTableFieldNotFoundExceptionTest extends TestCase
{
    #[Test]
    public function forField(): void
    {
        $actual = ProcessTableFieldNotFoundException::forField('someField', 'someProcess');

        self::assertSame('Process table field "someField" is not configured in process link "someProcess".', $actual->getMessage());
        self::assertSame(1582053551, $actual->getCode());
    }
}
