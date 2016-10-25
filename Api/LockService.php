<?php

namespace SnowIO\Lock\Api;

interface LockService
{
    public function acquireLock($lock, $timeout) : bool;

    public function releaseLock($lock);
}
