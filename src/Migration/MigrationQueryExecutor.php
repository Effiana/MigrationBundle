<?php

namespace Effiana\MigrationBundle\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Psr\Log\LoggerInterface;

class MigrationQueryExecutor
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Connection $connection
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * Gets a connection object this migration query executor works with
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Executes the given query
     *
     * @param string|MigrationQuery $query
     * @param bool $dryRun
     * @throws DBALException
     */
    public function execute($query, $dryRun): void
    {
        if ($query instanceof MigrationQuery) {
            if ($query instanceof ConnectionAwareInterface) {
                $query->setConnection($this->connection);
            }
            if ($dryRun) {
                $descriptions = $query->getDescription();
                if (!empty($descriptions)) {
                    foreach ((array)$descriptions as $description) {
                        $this->logger->info($description);
                    }
                }
            } else {
                $query->execute($this->logger);
            }
        } else {
            $this->logger->info($query);
            if (!$dryRun) {
                $this->connection->executeQuery($query);
            }
        }
    }
}
