<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Entity;

final class Step
{
    private function __construct(
        public readonly int $uid,
        public readonly string $handle,
        public readonly string $name,
        public readonly int $processUid,
        public readonly int $stepNumber,
        public readonly bool $disabled,
        public readonly ?Process $process = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int)$data['uid'],
            $data['handle'],
            $data['name'],
            (int)$data['process'],
            (int)$data['step_number'],
            (bool)$data['disabled'],
        );
    }

    public function withProcess(Process $process): self
    {
        return new self(
            $this->uid,
            $this->handle,
            $this->name,
            $this->processUid,
            $this->stepNumber,
            $this->disabled,
            $process,
        );
    }
}
