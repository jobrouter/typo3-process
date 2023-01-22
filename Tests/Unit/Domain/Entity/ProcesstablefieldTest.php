<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Domain\Entity;

use Brotkrueml\JobRouterBase\Enumeration\FieldType;
use Brotkrueml\JobRouterProcess\Domain\Entity\ProcessTableField;
use PHPUnit\Framework\TestCase;

final class ProcesstablefieldTest extends TestCase
{
    /**
     * @test
     */
    public function fromArray(): void
    {
        $actual = ProcessTableField::fromArray([
            'uid' => '1',
            'name' => 'some name',
            'description' => 'some description',
            'type' => 3,
            'field_size' => 2,
        ]);

        self::assertSame(1, $actual->uid);
        self::assertSame('some name', $actual->name);
        self::assertSame('some description', $actual->description);
        self::assertSame(FieldType::Decimal, $actual->type);
        self::assertSame(2, $actual->fieldSize);
    }
}
