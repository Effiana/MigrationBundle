<?php

namespace Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage;

use Effiana\MigrationBundle\Migration\Migration;
use Effiana\MigrationBundle\Migration\QueryBag;
use Doctrine\DBAL\Schema\Schema;

class InvalidIndexMigration implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->createTable('index_table');
        $table->addColumn('key', 'string', ['length' => 500]);
        $table->addIndex(['key'], 'index');
    }
}
