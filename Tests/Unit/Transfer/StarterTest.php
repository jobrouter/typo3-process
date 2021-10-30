<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Transfer;

use Brotkrueml\JobRouterConnector\RestClient\RestClientFactory;
use Brotkrueml\JobRouterProcess\Crypt\Transfer\Decrypter;
use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Transfer\Starter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class StarterTest extends TestCase
{
    /**
     * @var Starter
     */
    private $subject;

    /**
     * @var MockObject|PersistenceManagerInterface
     */
    private $persistenceManagerMock;

    /**
     * @var MockObject|TransferRepository
     */
    private $transferRepositoryMock;

    /**
     * @var MockObject|StepRepository
     */
    private $stepRepositoryMock;

    /**
     * @var MockObject|Decrypter
     */
    private $decrypter;

    protected function setUp(): void
    {
        $this->persistenceManagerMock = $this->createMock(PersistenceManagerInterface::class);

        $this->stepRepositoryMock = $this->getMockBuilder(StepRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->decrypter = $this->createMock(Decrypter::class);

        $this->transferRepositoryMock = $this->getMockBuilder(TransferRepository::class)
            ->disableOriginalConstructor()
            ->addMethods(['findByStartSuccess'])
            ->onlyMethods(['update'])
            ->getMock();

        $this->subject = new Starter(
            $this->persistenceManagerMock,
            new RestClientFactory(),
            $this->stepRepositoryMock,
            $this->decrypter,
            $this->transferRepositoryMock
        );
        $this->subject->setLogger(new NullLogger());
    }

    /**
     * @test
     */
    public function runWithNoTransfersAvailableReturns0TotalsAndErrors(): void
    {
        $this->transferRepositoryMock
            ->method('findByStartSuccess')
            ->willReturn([]);

        $actual = $this->subject->run();

        self::assertSame(0, $actual->total);
        self::assertSame(0, $actual->errors);
    }
}
