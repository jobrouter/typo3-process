<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Unit\Exception;

use JobRouter\AddOn\Typo3Process\Exception\CommonParameterNotFoundException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CommonParameterNotFoundExceptionTest extends TestCase
{
    #[Test]
    public function forMethod(): void
    {
        $actual = CommonParameterNotFoundException::forMethod('someMethod');

        self::assertSame('Method "someMethod" in Transfer DTO not found', $actual->getMessage());
        self::assertSame(1581703904, $actual->getCode());
    }
}
