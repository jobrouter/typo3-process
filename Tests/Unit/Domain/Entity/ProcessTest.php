<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Entity;

use Brotkrueml\JobRouterConnector\Domain\Entity\Connection;
use Brotkrueml\JobRouterProcess\Domain\Entity\Process;
use Brotkrueml\JobRouterProcess\Domain\Entity\Processtablefield;
use PHPUnit\Framework\TestCase;

final class ProcessTest extends TestCase
{
    /**
     * @test
     */
    public function fromArray(): void
    {
        $actual = Process::fromArray([
            'uid' => '1',
            'name' => 'some name',
            'connection' => '42',
            'disabled' => 0,
        ]);

        self::assertSame(1, $actual->uid);
        self::assertSame('some name', $actual->name);
        self::assertSame(42, $actual->connectionUid);
        self::assertFalse($actual->disabled);
        self::assertNull($actual->connection);
        self::assertNull($actual->processtablefields);
    }

    /**
     * @test
     */
    public function withConnection(): void
    {
        $connection = Connection::fromArray([
            'uid' => 42,
            'name' => '',
            'handle' => '',
            'base_url' => '',
            'username' => '',
            'password' => '',
            'timeout' => 0,
            'verify' => true,
            'proxy' => '',
            'jobrouter_version' => '',
            'disabled' => false,
        ]);

        $actual = Process::fromArray([
            'uid' => 1,
            'name' => 'some name',
            'connection' => 42,
            'disabled' => false,
        ])->withConnection($connection);

        self::assertSame($connection, $actual->connection);
    }

    /**
     * @test
     */
    public function withProcesstablefields(): void
    {
        $fields = [
            Processtablefield::fromArray([
                'uid' => 21,
                'name' => '',
                'description' => '',
                'type' => 1,
                'field_size' => 0,
            ]),
        ];

        $actual = Process::fromArray([
            'uid' => 1,
            'name' => 'some name',
            'connection' => 42,
            'disabled' => false,
        ])->withProcesstablefields($fields);

        self::assertSame($fields, $actual->processtablefields);
    }
}
