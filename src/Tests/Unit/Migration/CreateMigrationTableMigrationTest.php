<?php

namespace Effiana\MigrationBundle\Tests\Unit\Migration;

use Effiana\MigrationBundle\Migration\CreateMigrationTableMigration;
use Effiana\MigrationBundle\Migration\QueryBag;
use Doctrine\DBAL\Schema\Schema;

class CreateMigrationTableMigrationTest extends \PHPUnit\Framework\TestCase
{
    public function testUp()
    {
        $schema          = new Schema();
        $queryBag        = new QueryBag();
        $createMigration = new CreateMigrationTableMigration();
        $createMigration->up($schema, $queryBag);

        $this->assertEmpty($queryBag->getPreQueries());
        $this->assertEmpty($queryBag->getPostQueries());

        $table = $schema->getTable(CreateMigrationTableMigration::MIGRATION_TABLE);
        $this->assertTrue($table->hasColumn('id'));
        $this->assertTrue($table->hasColumn('bundle'));
        $this->assertTrue($table->hasColumn('version'));
        $this->assertTrue($table->hasColumn('loaded_at'));
    }
}
