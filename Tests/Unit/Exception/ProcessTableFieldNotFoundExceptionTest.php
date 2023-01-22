<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Exception;

use Brotkrueml\JobRouterProcess\Exception\ProcessTableFieldNotFoundException;
use PHPUnit\Framework\TestCase;

final class ProcessTableFieldNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function forField(): void
    {
        $actual = ProcessTableFieldNotFoundException::forField('someField', 'someProcess');

        self::assertSame('Process table field "someField" is not configured in process link "someProcess".', $actual->getMessage());
        self::assertSame(1582053551, $actual->getCode());
    }
}
