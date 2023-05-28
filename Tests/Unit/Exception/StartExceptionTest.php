<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Exception;

use Brotkrueml\JobRouterProcess\Exception\StartException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StartExceptionTest extends TestCase
{
    #[Test]
    public function forUnavailableConnection(): void
    {
        $actual = StartException::forUnavailableConnection('some_process');

        self::assertSame('The connection for process with name "some_process" is not available.', $actual->getMessage());
        self::assertSame(1674845516, $actual->getCode());
    }
}
