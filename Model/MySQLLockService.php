<?php

namespace SnowIO\Lock\Model;

use Magento\Framework\App\ResourceConnection;
use SnowIO\Lock\Api\LockService;

class MySQLLockService implements LockService
{
    private $connection;
    private $currentDatabase;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    public function acquireLock(string $name, int $timeout) : bool
    {
        $statement = sprintf("SELECT GET_LOCK(%s, %u)", $this->prepareLockName($name), $timeout);
        $result = $this->connection->fetchOne($statement);

        return $result === '1';
    }

    public function releaseLock(string $name)
    {
        $statement = "SELECT RELEASE_LOCK({$this->prepareLockName($name)})";
        $this->connection->query($statement);
    }

    private function prepareLockName(string $lockName) : string
    {
        return $this->connection->quote("{$this->getCurrentDatabase()}.$lockName");
    }

    private function getCurrentDatabase()
    {
        if (null === $this->currentDatabase) {
            $this->currentDatabase = $this->connection->fetchOne('SELECT DATABASE()');
        }

        return $this->currentDatabase;
    }
}
