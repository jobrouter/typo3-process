<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Unit\Exception;

use JobRouter\AddOn\Typo3Process\Exception\PrepareException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PrepareExceptionTest extends TestCase
{
    #[Test]
    public function forUid(): void
    {
        $actual = PrepareException::forNotWritable();

        self::assertSame('Transfer record cannot be written, see log file for details.', $actual->getMessage());
        self::assertSame(1581278897, $actual->getCode());
    }
}
