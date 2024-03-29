<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JobRouter\AddOn\Typo3Process\Tests\Unit\Domain\Entity;

use JobRouter\AddOn\Typo3Process\Domain\Entity\Step;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StepTest extends TestCase
{
    #[Test]
    public function fromArray(): void
    {
        $actual = Step::fromArray([
            'uid' => '1',
            'handle' => 'some_handle',
            'name' => 'some name',
            'process' => '21',
            'step_number' => '42',
            'disabled' => '1',
        ]);

        self::assertSame(1, $actual->uid);
        self::assertSame('some_handle', $actual->handle);
        self::assertSame('some name', $actual->name);
        self::assertSame(21, $actual->processUid);
        self::assertSame(42, $actual->stepNumber);
        self::assertTrue($actual->disabled);
    }
}
