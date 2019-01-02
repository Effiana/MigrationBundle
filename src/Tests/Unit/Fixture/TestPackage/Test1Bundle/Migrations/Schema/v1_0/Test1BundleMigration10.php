<?php

namespace Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage\Test1Bundle\Migrations\Schema\v1_0;

use Effiana\MigrationBundle\Migration\Migration;
use Effiana\MigrationBundle\Migration\QueryBag;
use Doctrine\DBAL\Schema\Schema;

class Test1BundleMigration10 implements Migration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $queries->addQuery('CREATE TABLE TEST (id INT AUTO_INCREMENT NOT NULL)');
    }
}
