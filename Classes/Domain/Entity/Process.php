<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Entity;

use Brotkrueml\JobRouterConnector\Domain\Entity\Connection;

final class Process
{
    /**
     * @param Processtablefield[]|null $processtablefields
     */
    private function __construct(
        public readonly int $uid,
        public readonly string $name,
        public readonly int $connectionUid,
        public readonly bool $disabled,
        public readonly ?Connection $connection = null,
        public readonly ?array $processtablefields = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int)$data['uid'],
            $data['name'],
            (int)$data['connection'],
            (bool)$data['disabled'],
        );
    }

    public function withConnection(Connection $connection): self
    {
        return new self(
            $this->uid,
            $this->name,
            $this->connectionUid,
            $this->disabled,
            $connection,
            $this->processtablefields,
        );
    }

    /**
     * @param Processtablefield[] $processtablefields
     */
    public function withProcesstablefields(array $processtablefields): self
    {
        return new self(
            $this->uid,
            $this->name,
            $this->connectionUid,
            $this->disabled,
            $this->connection,
            $processtablefields,
        );
    }
}
