<?php

namespace Effiana\MigrationBundle\Tests\Unit\Migration\Fixtures\Extension;

use Effiana\MigrationBundle\Migration\Extension\DatabasePlatformAwareInterface;
use Effiana\MigrationBundle\Migration\Extension\NameGeneratorAwareInterface;
use Effiana\MigrationBundle\Tools\DbIdentifierNameGenerator;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class TestExtension implements DatabasePlatformAwareInterface, NameGeneratorAwareInterface
{
    protected $platform;

    protected $nameGenerator;

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
}
