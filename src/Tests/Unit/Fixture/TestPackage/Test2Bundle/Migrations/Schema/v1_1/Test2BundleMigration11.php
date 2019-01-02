<?php

namespace Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage\Test2Bundle\Migrations\Schema\v1_1;

use Effiana\MigrationBundle\Migration\Migration;
use Effiana\MigrationBundle\Migration\OrderedMigrationInterface;
use Effiana\MigrationBundle\Migration\QueryBag;
use Doctrine\DBAL\Schema\Schema;

class Test2BundleMigration11 implements Migration, OrderedMigrationInterface
{
    public function getOrder()
    {
        return 2;
    }

    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('test1table');
        $table->addColumn('another_column', 'int');
    }
}
