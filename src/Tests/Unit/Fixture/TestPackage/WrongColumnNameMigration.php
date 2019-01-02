<?php

namespace Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage;

use Effiana\MigrationBundle\Migration\Migration;
use Effiana\MigrationBundle\Migration\QueryBag;
use Doctrine\DBAL\Schema\Schema;

class WrongColumnNameMigration implements Migration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->createTable('wrong_table');
        $table->addColumn('extra_long_column_bigger_30_chars', 'integer');
    }
}
