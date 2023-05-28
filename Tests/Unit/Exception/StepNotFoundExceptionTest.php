<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Exception;

use Brotkrueml\JobRouterProcess\Exception\StepNotFoundException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StepNotFoundExceptionTest extends TestCase
{
    #[Test]
    public function forUid(): void
    {
        $actual = StepNotFoundException::forUid(42);

        self::assertSame('Step with uid "42" not found.', $actual->getMessage());
        self::assertSame(1674204416, $actual->getCode());
    }

    #[Test]
    public function forHandle(): void
    {
        $actual = StepNotFoundException::forHandle('some_handle');

        self::assertSame('Step with handle "some_handle" not found.', $actual->getMessage());
        self::assertSame(1674204417, $actual->getCode());
    }
}
