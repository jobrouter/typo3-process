<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Exception;

use Brotkrueml\JobRouterProcess\Exception\DecryptException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DecryptExceptionTest extends TestCase
{
    #[Test]
    public function forField(): void
    {
        $previous = new \Exception('some exception message');
        $actual = DecryptException::forField('someField', $previous);

        self::assertSame('Field "someField" in transfer cannot be decrypted, reason: some exception message', $actual->getMessage());
        self::assertSame(1599323431, $actual->getCode());
        self::assertSame($previous, $actual->getPrevious());
    }
}
