<?php

namespace SnowIO\Lock\Model;

use Magento\Framework\App\ResourceConnection;

class MySQLLockService extends AbstractLockService
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
        $lockName = "{$this->getCurrentDatabase()}.$lockName";
        if (strlen($lockName) >= 64) {
            $lockName = hash('sha256', $lockName);
        }
        return $this->connection->quote($lockName);
    }

    private function getCurrentDatabase()
    {
        if (null === $this->currentDatabase) {
            $this->currentDatabase = $this->connection->fetchOne('SELECT DATABASE()');
        }

        return $this->currentDatabase;
    }
}
