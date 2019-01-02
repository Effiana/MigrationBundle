<?php

namespace Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage;

use Effiana\MigrationBundle\Migration\Migration;
use Effiana\MigrationBundle\Migration\QueryBag;
use Doctrine\DBAL\Schema\Schema;

class UpdatedColumnIndexMigration implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('index_table2');
        $table->getColumn('key')->setLength(500);
        $table->addIndex(['key'], 'index2');
    }
}
