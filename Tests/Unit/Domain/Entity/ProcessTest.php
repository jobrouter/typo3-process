<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Entity;

use Brotkrueml\JobRouterProcess\Domain\Entity\Process;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProcessTest extends TestCase
{
    #[Test]
    public function fromArray(): void
    {
        $actual = Process::fromArray([
            'uid' => '1',
            'name' => 'some name',
            'connection' => '42',
            'disabled' => 0,
        ]);

        self::assertSame(1, $actual->uid);
        self::assertSame('some name', $actual->name);
        self::assertSame(42, $actual->connectionUid);
        self::assertFalse($actual->disabled);
    }
}
