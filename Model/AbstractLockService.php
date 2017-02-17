<?php

namespace SnowIO\Lock\Model;

use SnowIO\Lock\Api\LockService;

abstract class AbstractLockService implements LockService
{
    public function acquireLocks(array $lockNames, int $totalTimeout) : bool
    {
        \sort($lockNames); // avoid deadlocks by always acquiring locks in consistent order

        if ($totalTimeout <= 0) {
            return $this->acquireLocksWithoutWaiting($lockNames);
        }

        return $this->acquireLocksWithTimeout($lockNames, $totalTimeout);
    }

    public function releaseLocks(array $lockNames)
    {
        foreach ($lockNames as $lockName) {
            $this->releaseLock($lockName);
        }
    }

    private function acquireLocksWithTimeout(array $lockNames, int $totalTimeout) : bool
    {
        $startTime = \microtime(true);

        foreach ($lockNames as $index => $lockName) {
            $runtime = \microtime(true) - $startTime;
            $timeout = \max(0, \round($totalTimeout - $runtime));
            if (!$this->acquireLock($lockName, $timeout)) {
                $this->releaseLocks(\array_slice($lockNames, 0, $index));
                return false;
            }
        }

        return true;
    }

    private function acquireLocksWithoutWaiting(array $lockNames)
    {
        foreach ($lockNames as $index => $lockName) {
            if (!$this->acquireLock($lockName, 0)) {
                $this->releaseLocks(\array_slice($lockNames, 0, $index));
                return false;
            }
        }

        return true;
    }
}
