<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Exception;

final class FileNotFoundException extends \RuntimeException
{
    public static function forIdentifier(string $identifier): self
    {
        return new self(
            \sprintf('File with identifier "%s" is not available!', $identifier),
            1664109447,
        );
    }
}
