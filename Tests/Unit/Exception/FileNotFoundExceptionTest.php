<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Unit\Exception;

use JobRouter\AddOn\Typo3Process\Exception\FileNotFoundException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FileNotFoundExceptionTest extends TestCase
{
    #[Test]
    public function forIdentifier(): void
    {
        $actual = FileNotFoundException::forIdentifier('someIdentifier');

        self::assertSame('File with identifier "someIdentifier" is not available!', $actual->getMessage());
        self::assertSame(1664109447, $actual->getCode());
    }
}
