<?php

namespace Effiana\MigrationBundle\Migration\Schema;

use Effiana\MigrationBundle\Tools\DbIdentifierNameGenerator;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\SchemaConfig;
use Doctrine\DBAL\Schema\Sequence;

class SchemaWithNameGenerator extends Schema
{
    public const TABLE_CLASS = TableWithNameGenerator::class;

    /**
     * @var DbIdentifierNameGenerator
     */
    protected $nameGenerator;

    /**
     * @param DbIdentifierNameGenerator $nameGenerator
     * @param Table[]                   $tables
     * @param Sequence[]                $sequences
     * @param SchemaConfig              $schemaConfig
     */
    public function __construct(
        DbIdentifierNameGenerator $nameGenerator,
        array $tables = [],
        array $sequences = [],
        SchemaConfig $schemaConfig = null
    ) {
        $this->nameGenerator = $nameGenerator;

        parent::__construct($tables, $sequences, $schemaConfig);
    }

    /**
     * {@inheritdoc}
     */
    protected function createTableObject(array $args): \Doctrine\DBAL\Schema\Table
    {
        $args['nameGenerator'] = $this->nameGenerator;

        return parent::createTableObject($args);
    }

    /**
     * {@inheritdoc}
     * @throws DBALException
     */
    public function renameTable($oldTableName, $newTableName)
    {
        throw new DBALException(
            'Schema#renameTable() was removed, because it drops and recreates ' .
            'the table instead. There is no fix available, because a schema diff cannot reliably detect if a ' .
            'table was renamed or one table was created and another one dropped.'
        );
    }
}
