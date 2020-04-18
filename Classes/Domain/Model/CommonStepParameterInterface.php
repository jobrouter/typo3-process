<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\Model;

interface CommonStepParameterInterface
{
    public function getInitiator(): string;

    public function setInitiator(string $initiator): void;

    public function getUsername(): string;

    public function setUsername(string $username): void;

    public function getJobfunction(): string;

    public function setJobfunction(string $jobfunction): void;

    public function getSummary(): string;

    public function setSummary(string $summary): void;

    public function getPriority(): int;

    public function setPriority(int $priority): void;

    public function getPool(): int;

    public function setPool(int $pool): void;

    public function getDefaultParameters(): array;
}
