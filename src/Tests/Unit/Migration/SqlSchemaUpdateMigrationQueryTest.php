<?php

namespace Effiana\MigrationBundle\Tests\Unit\Migration;

use Effiana\MigrationBundle\Migration\SqlSchemaUpdateMigrationQuery;

class SqlSchemaUpdateMigrationQueryTest extends \PHPUnit\Framework\TestCase
{
    public function testIsUpdateRequired()
    {
        $query = new SqlSchemaUpdateMigrationQuery('ALTER TABLE');

        $this->assertInstanceOf('Effiana\MigrationBundle\Migration\SqlMigrationQuery', $query);
        $this->assertInstanceOf('Effiana\MigrationBundle\Migration\SchemaUpdateQuery', $query);
        $this->assertTrue($query->isUpdateRequired());
    }
}
