<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Finishers;

use Brotkrueml\JobRouterProcess\Domain\Finishers\StartInstanceFinisher;
use Brotkrueml\JobRouterProcess\Exception\MissingFinisherOptionException;
use Brotkrueml\JobRouterProcess\Transfer\Preparer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Finishers\FinisherContext;

class StartInstanceFinisherTest extends TestCase
{
    /** @var StartInstanceFinisher */
    private $subject;

    /** @var MockObject|FinisherContext */
    private $finisherContextMock;

    /** @var MockObject|Preparer */
    private $preparerMock;

    protected function setUp(): void
    {
        $this->subject = new StartInstanceFinisher('JobRouterStartInstance');
        $this->subject->setLogger(new NullLogger());

        $this->finisherContextMock = $this->createMock(FinisherContext::class);

        $this->preparerMock = $this->getMockBuilder(Preparer::class)
            ->disableOriginalConstructor()
            ->getMock();
        GeneralUtility::addInstance(Preparer::class, $this->preparerMock);
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenHandleIsNotGiven(): void
    {
        $this->expectException(MissingFinisherOptionException::class);
        $this->expectExceptionCode(1581270462);

        $this->subject->setOption('dummy', ['dummy']);
        $this->subject->execute($this->finisherContextMock);
    }
}
