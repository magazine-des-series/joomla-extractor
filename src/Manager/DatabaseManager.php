<?php

declare(strict_types=1);

namespace Manager;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Query\QueryBuilder;

class DatabaseManager
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        try {
            if ($this->connection->ping()) {
                return true;
            }

            return false;
        } catch (ConnectionException $exception) {
            return false;
        }
    }
}
