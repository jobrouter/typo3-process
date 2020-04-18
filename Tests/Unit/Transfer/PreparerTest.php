<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Transfer;

use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
use Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository;
use Brotkrueml\JobRouterProcess\Exception\PrepareException;
use Brotkrueml\JobRouterProcess\Transfer\Preparer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\NullLogger;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class PreparerTest extends TestCase
{
    /** @var Preparer */
    private $subject;

    /** @var ObjectProphecy */
    private $persistenceManager;

    /** @var ObjectProphecy */
    private $transferRepository;

    protected function setUp(): void
    {
        $this->persistenceManager = $this->prophesize(PersistenceManager::class);
        $this->transferRepository = $this->prophesize(TransferRepository::class);

        $this->subject = new Preparer(
            $this->persistenceManager->reveal(),
            $this->transferRepository->reveal()
        );
        $this->subject->setLogger(new NullLogger());
    }

    /**
     * @test
     */
    public function storePersistsRecordCorrectly(): void
    {
        $transfer = new Transfer();
        $transfer->setStepUid(42);
        $transfer->setIdentifier('some identifier');
        $transfer->setProcesstable('some data');

        $this->persistenceManager->persistAll()->shouldBeCalled();
        $this->transferRepository->add($transfer)->shouldBeCalled();

        $this->subject->store($transfer);
    }

    /**
     * @test
     */
    public function storeThrowsExceptionOnError(): void
    {
        $this->expectException(PrepareException::class);
        $this->expectExceptionCode(1581278897);

        $this->transferRepository->add(Argument::any())->willThrow(\Exception::class);

        $this->subject->store(new Transfer());
    }
}
