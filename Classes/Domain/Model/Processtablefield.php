<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Processtablefield extends AbstractEntity
{
    /** @var string */
    protected $name = '';

    /** @var string */
    protected $description = '';

    /** @var int */
    protected $type = 0;

    /** @var int */
    protected $fieldSize = 0;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getFieldSize(): int
    {
        return $this->fieldSize;
    }

    public function setFieldSize(int $fieldSize): void
    {
        $this->fieldSize = $fieldSize;
    }
}
