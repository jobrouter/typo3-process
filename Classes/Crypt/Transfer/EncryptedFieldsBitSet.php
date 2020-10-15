<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Crypt\Transfer;

use TYPO3\CMS\Core\Type\BitSet;

final class EncryptedFieldsBitSet extends BitSet
{
    public const NONE = 0;
    public const PROCESSTABLE = 1 << 0;
    public const SUMMARY = 1 << 1;
}
