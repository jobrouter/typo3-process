<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Widgets;

/**
 * @internal
 */
class TransferStatus
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $count;

    /**
     * @var string
     */
    private $colour;

    public function __construct(string $name, string $colour)
    {
        $this->name = $name;
        $this->colour = $colour;
        $this->count = 0;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getColour(): string
    {
        return $this->colour;
    }
}
