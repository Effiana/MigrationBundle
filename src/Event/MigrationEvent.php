<?php

namespace Effiana\MigrationBundle\Event;

use Doctrine\DBAL\Connection;
use Effiana\MigrationBundle\Tools\SafeDatabaseChecker;
use Effiana\MigrationBundle\Migration\Migration;
use Symfony\Contracts\EventDispatcher\Event;

class MigrationEvent extends Event
{
    /**
     * @var Migration[]
     */
    protected $migrations = [];

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Adds a migration
     *
     * @param Migration $migration
     * @param bool      $prepend If TRUE a migration is added to the beginning of the list
     */
    public function addMigration(Migration $migration, $prepend = false): void
    {
        if ($prepend) {
            array_unshift($this->migrations, $migration);
        } else {
            $this->migrations[] = $migration;
        }
    }

    /**
     * Gets all migrations
     *
     * @return Migration[]
     */
    public function getMigrations(): array
    {
        return $this->migrations;
    }

    /**
     * Prepares and executes an SQL query and returns the result as an associative array.
     *
     * @param string $sql    The SQL query.
     * @param array  $params The query parameters.
     * @param array  $types  The query parameter types.
     * @return array
     */
    public function getData($sql, array $params = array(), $types = array()): array
    {
        $this->connection->connect();

        return $this->connection->fetchAll($sql, $params, $types);
    }

    /**
     * Check if the given table exists in a database
     *
     * @param string $tableName
     * @return bool TRUE if a table exists; otherwise, FALSE
     */
    public function isTableExist($tableName): bool
    {
        return SafeDatabaseChecker::tablesExist($this->connection, $tableName);
    }
}
