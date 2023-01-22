<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Exception;

use Brotkrueml\JobRouterProcess\Exception\CommonParameterNotFoundException;
use PHPUnit\Framework\TestCase;

final class CommonParameterNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function forMethod(): void
    {
        $actual = CommonParameterNotFoundException::forMethod('someMethod');

        self::assertSame('Method "someMethod" in Transfer DTO not found', $actual->getMessage());
        self::assertSame(1581703904, $actual->getCode());
    }
}
