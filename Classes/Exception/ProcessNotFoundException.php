<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Exception;

final class ProcessNotFoundException extends \RuntimeException
{
    public static function forUid(int $uid): self
    {
        return new self(
            \sprintf(
                'Process with uid "%d" not found.',
                $uid,
            ),
            1674201139,
        );
    }
}
