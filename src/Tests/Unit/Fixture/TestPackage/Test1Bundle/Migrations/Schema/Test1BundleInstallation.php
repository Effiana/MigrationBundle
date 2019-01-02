<?php

namespace Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage\Test1Bundle\Migrations\Schema;

use Effiana\MigrationBundle\Migration\Installation;
use Effiana\MigrationBundle\Migration\QueryBag;
use Doctrine\DBAL\Schema\Schema;

class Test1BundleInstallation implements Installation
{
    /**
     * @inheritdoc
     */
    public function getMigrationVersion()
    {
        return "v1_0";
    }

    /**
     * @inheritdoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $queries->addQuery('CREATE TABLE TEST (id INT AUTO_INCREMENT NOT NULL)');
    }
}
