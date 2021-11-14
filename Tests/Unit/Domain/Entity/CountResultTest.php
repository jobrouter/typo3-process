<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Entity;

use Brotkrueml\JobRouterProcess\Domain\Entity\CountResult;
use PHPUnit\Framework\TestCase;

final class CountResultTest extends TestCase
{
    /**
     * @test
     */
    public function constructSetsPropertiesCorrectly(): void
    {
        $subject = new CountResult(42, 12);

        self::assertSame(42, $subject->total);
        self::assertSame(12, $subject->errors);
    }
}