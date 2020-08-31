<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Widgets;

use Brotkrueml\JobRouterProcess\Widgets\TransferStatus;
use PHPUnit\Framework\TestCase;

class TransferStatusTest extends TestCase
{
    /**
     * @test
     */
    public function statusIsInitialisedCorrectly(): void
    {
        $subject = new TransferStatus('some status', '#abcdef');

        self::assertSame('some status', $subject->getName());
        self::assertSame('#abcdef', $subject->getColour());
        self::assertSame(0, $subject->getCount());
    }

    /**
     * @test
     */
    public function setAndGetCountImplementedCorrectly(): void
    {
        $subject = new TransferStatus('', '');
        $subject->setCount(42);

        self::assertSame(42, $subject->getCount());
    }
}
