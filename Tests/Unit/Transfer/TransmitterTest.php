<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Transfer;

use Brotkrueml\JobRouterProcess\Domain\Repository\InstanceRepository;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Transfer\Transmitter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class TransmitterTest extends TestCase
{
    /** @var Transmitter */
    private $subject;

    /** @var MockObject|PersistenceManagerInterface */
    private $persistenceManagerMock;

    /** @var MockObject|TransferRepository */
    private $transferRepositoryMock;

    /** @var MockObject|InstanceRepository */
    private $instanceRepositoryMock;

    protected function setUp(): void
    {
        $this->persistenceManagerMock = $this->createMock(PersistenceManagerInterface::class);

        $this->transferRepositoryMock = $this->getMockBuilder(TransferRepository::class)
            ->disableOriginalConstructor()
            ->addMethods(['findByTransmitSuccess'])
            ->onlyMethods(['update'])
            ->getMock();

        $this->instanceRepositoryMock = $this->getMockBuilder(InstanceRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subject = new Transmitter(
            $this->persistenceManagerMock,
            $this->transferRepositoryMock,
            $this->instanceRepositoryMock
        );
        $this->subject->setLogger(new NullLogger());
    }

    /**
     * @test
     */
    public function transmitWithNoTransfersAvailableReturns0TotalsAndErrors(): void
    {
        $this->transferRepositoryMock
            ->method('findByTransmitSuccess')
            ->willReturn([]);

        [$total, $errors] = $this->subject->run();

        self::assertSame(0, $total);
        self::assertSame(0, $errors);
    }
}