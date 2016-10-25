<?php

namespace SnowIO\Lock\Api;

interface LockService
{
    public function acquireLock(string $name, int $timeout) : bool;

    public function releaseLock(string $name);
}
