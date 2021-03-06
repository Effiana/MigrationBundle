<?php

namespace Effiana\MigrationBundle\Migration;

use Doctrine\DBAL\Schema\Schema;

class CreateMigrationTableMigration implements Migration
{
    const MIGRATION_TABLE = 'effiana_migrations';

    /**
     * @inheritdoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->createTable(self::MIGRATION_TABLE);
        $table->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $table->addColumn('bundle', 'string', ['notnull' => true, 'length' => 250]);
        $table->addColumn('version', 'string', ['notnull' => true, 'length' => 250]);
        $table->addColumn('loaded_at', 'datetime', ['notnull' => true]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['bundle'], 'idx_effiana_migrations', []);
    }
}
