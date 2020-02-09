<?php
declare(strict_types=1);

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Model;

use Brotkrueml\JobRouterProcess\Domain\Model\Processtablefield;
use PHPUnit\Framework\TestCase;

class ProcesstablefieldTest extends TestCase
{
    /** @var Processtablefield */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new Processtablefield();
    }

    /**
     * @test
     */
    public function getAndSetName(): void
    {
        self::assertSame('', $this->subject->getName());

        $this->subject->setName('some name');

        self::assertSame('some name', $this->subject->getName());
    }

    /**
     * @test
     */
    public function getAndSetDescription(): void
    {
        self::assertSame('', $this->subject->getDescription());

        $this->subject->setDescription('some description');

        self::assertSame('some description', $this->subject->getDescription());
    }

    /**
     * @test
     */
    public function getAndSetType(): void
    {
        self::assertSame(0, $this->subject->getType());

        $this->subject->setType(2);

        self::assertSame(2, $this->subject->getType());
    }
}
