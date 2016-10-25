<?php

namespace SnowIO\Lock\Api;

interface LockService
{
    public function acquireLock(string $lock, int $timeout) : bool;

    public function releaseLock(string $lock);
}
