<?php

namespace SnowIO\Lock\Model;

use Magento\Framework\App\ResourceConnection;
use SnowIO\Lock\Api\LockService;

class MySQLLockService implements LockService
{
    private $connection;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    public function acquireLock($lock, $timeout) : bool
    {
        $statement = sprintf("SELECT GET_LOCK(%s, %u)", $this->connection->quote($lock), $this->connection->quote($timeout, 'INTEGER'));
        $result = $this->connection->fetchOne($statement);

        return $result === '1';
    }

    public function releaseLock($lock)
    {
        $statement = sprintf("SELECT RELEASE_LOCK(%s)", $this->connection->quote($lock));
        $this->connection->query($statement);
    }
}