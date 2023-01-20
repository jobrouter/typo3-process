<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Dto;

/**
 * @internal
 */
final class CountResult
{
    public function __construct(
        public readonly int $total,
        public readonly int $errors,
    ) {
    }
}