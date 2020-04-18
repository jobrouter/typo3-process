<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Event;

final class ResolveFinisherVariableEvent
{
    /** @var int */
    private $fieldType;

    /** @var string|int */
    private $value;

    /** @var string */
    private $transferIdentifier;

    public function __construct(int $fieldType, $value, string $transferIdentifier)
    {
        $this->fieldType = $fieldType;
        $this->value = $value;
        $this->transferIdentifier = $transferIdentifier;
    }

    public function getFieldType(): int
    {
        return $this->fieldType;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getTransferIdentifier(): string
    {
        return $this->transferIdentifier;
    }
}
