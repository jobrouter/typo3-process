<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Exception;

use Brotkrueml\JobRouterProcess\Exception\MissingFinisherOptionException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MissingFinisherOptionExceptionTest extends TestCase
{
    #[Test]
    public function forStepWithFormIdentifier(): void
    {
        $actual = MissingFinisherOptionException::forStepWithFormIdentifier('someIdentifier');

        self::assertSame('Step handle of form with identifier "someIdentifier" is not defined.', $actual->getMessage());
        self::assertSame(1581270462, $actual->getCode());
    }
}
