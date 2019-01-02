<?php

namespace Effiana\MigrationBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;
use Effiana\MigrationBundle\Migration\Migration;
use Effiana\MigrationBundle\Migration\QueryBag;

class EffianaMigrationBundle implements Migration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('effiana_migrations_data');
        $table->addColumn('version', 'string', ['notnull' => false, 'length' => 255]);
    }
}
