<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Event;

use Psr\Http\Message\ServerRequestInterface;

final class ResolveFinisherVariableEvent
{
    /** @var int */
    private $fieldType;

    /** @var string|int */
    private $value;

    /** @var string */
    private $transferIdentifier;

    /** @var array<string,string> */
    private $formValues;

    /** @var ServerRequestInterface */
    private $request;

    public function __construct(
        int $fieldType,
        $value,
        string $transferIdentifier,
        array $formValues,
        ServerRequestInterface $request
    ) {
        $this->fieldType = $fieldType;
        $this->value = $value;
        $this->transferIdentifier = $transferIdentifier;
        $this->formValues = $formValues;
        $this->request = $request;
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

    public function getFormValues(): array
    {
        return $this->formValues;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
