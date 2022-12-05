<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Functional\Domain\Repository\QueryBuilder;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class TransferRepositoryTest extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/jobrouter_connector',
        'typo3conf/ext/jobrouter_process',
    ];

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function testSomething(): void
    {
        self::assertTrue(true);
    }
}
