<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Unit\Exception;

use JobRouter\AddOn\Typo3Process\Exception\ProcessNotFoundException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProcessNotFoundExceptionTest extends TestCase
{
    #[Test]
    public function forUid(): void
    {
        $actual = ProcessNotFoundException::forUid(42);

        self::assertSame('Process with uid "42" not found.', $actual->getMessage());
        self::assertSame(1674201139, $actual->getCode());
    }
}
