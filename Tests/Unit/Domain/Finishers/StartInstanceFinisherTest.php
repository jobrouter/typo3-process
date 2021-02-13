<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Finishers;

use Brotkrueml\JobRouterBase\Domain\Correlation\IdGenerator;
use Brotkrueml\JobRouterBase\Domain\VariableResolvers\VariableResolver;
use Brotkrueml\JobRouterProcess\Domain\Finishers\StartInstanceFinisher;
use Brotkrueml\JobRouterProcess\Exception\MissingFinisherOptionException;
use Brotkrueml\JobRouterProcess\Transfer\Preparer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
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
        $GLOBALS['TYPO3_REQUEST'] = $this->createStub(ServerRequestInterface::class);

        $variableResolverStub = $this->createStub(VariableResolver::class);
        $variableResolverStub->method('setCorrelationId');
        $variableResolverStub->method('setFormValues');
        $variableResolverStub->method('setRequest');

        $identifierGeneratorStub = $this->createStub(IdGenerator::class);
        $identifierGeneratorStub
            ->method('build')
            ->willReturn('some-identifier');

        $this->subject = new StartInstanceFinisher('JobRouterStartInstance');
        $this->subject->injectVariableResolver($variableResolverStub);
        $this->subject->injectIdGenerator($identifierGeneratorStub);
        $this->subject->setLogger(new NullLogger());

        $this->finisherContextMock = $this->createMock(FinisherContext::class);

        $this->preparerMock = $this->getMockBuilder(Preparer::class)
            ->disableOriginalConstructor()
            ->getMock();
        GeneralUtility::addInstance(Preparer::class, $this->preparerMock);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        GeneralUtility::purgeInstances();
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
