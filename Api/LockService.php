<?php

namespace SnowIO\Lock\Api;

interface LockService
{
    public function acquireLock(string $name, int $timeout) : bool;

    public function acquireLocks(array $names, int $totalTimeout) : bool;

    public function releaseLock(string $name);

    public function releaseLocks(array $names);
}
