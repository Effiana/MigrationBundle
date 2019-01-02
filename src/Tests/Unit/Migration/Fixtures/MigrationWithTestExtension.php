<?php

namespace Effiana\MigrationBundle\Tests\Unit\Migration\Fixtures;

use Effiana\MigrationBundle\Migration\Extension\DatabasePlatformAwareInterface;
use Effiana\MigrationBundle\Migration\Extension\NameGeneratorAwareInterface;
use Effiana\MigrationBundle\Migration\Migration;
use Effiana\MigrationBundle\Migration\QueryBag;
use Effiana\MigrationBundle\Tests\Unit\Migration\Fixtures\Extension\TestExtension;
use Effiana\MigrationBundle\Tests\Unit\Migration\Fixtures\Extension\TestExtensionAwareInterface;
use Effiana\MigrationBundle\Tools\DbIdentifierNameGenerator;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Schema;

class MigrationWithTestExtension implements
    Migration,
    TestExtensionAwareInterface,
    DatabasePlatformAwareInterface,
    NameGeneratorAwareInterface
{
    protected $testExtension;

    protected $platform;

    protected $nameGenerator;

    public function setTestExtension(TestExtension $testExtension)
    {
        $this->testExtension = $testExtension;
    }

    public function getTestExtension()
    {
        return $this->testExtension;
    }

    public function setDatabasePlatform(AbstractPlatform $platform)
    {
        $this->platform = $platform;
    }

    public function getDatabasePlatform()
    {
        return $this->platform;
    }

    public function setNameGenerator(DbIdentifierNameGenerator $nameGenerator)
    {
        $this->nameGenerator = $nameGenerator;
    }

    public function getNameGenerator()
    {
        return $this->nameGenerator;
    }

    public function up(Schema $schema, QueryBag $queries)
    {
    }
}
