<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Exception;

use Brotkrueml\JobRouterProcess\Exception\MissingProcessTableFieldException;
use PHPUnit\Framework\TestCase;

final class MissingProcessTableFieldExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function forField(): void
    {
        $actual = MissingProcessTableFieldException::forField(
            'someProcessField',
            'someProcess',
            'someFormIdentifier',
        );

        self::assertSame('Process table field "someProcessField" is used in form with identifier "someFormIdentifier", but not defined in process link "someProcess".', $actual->getMessage());
        self::assertSame(1585930166, $actual->getCode());
    }
}