<?php

namespace Effiana\MigrationBundle\Tests\Unit\Migration\Fixtures;

use Effiana\MigrationBundle\Migration\Migration;
use Effiana\MigrationBundle\Migration\QueryBag;
use Effiana\MigrationBundle\Tests\Unit\Migration\Fixtures\Extension\TestExtensionDepended;
use Effiana\MigrationBundle\Tests\Unit\Migration\Fixtures\Extension\TestExtensionDependedAwareInterface;
use Doctrine\DBAL\Schema\Schema;

class MigrationWithTestExtensionDepended implements
    Migration,
    TestExtensionDependedAwareInterface
{
    protected $testExtensionDepended;

    public function setTestExtensionDepended(
        TestExtensionDepended $testExtensionDepended
    ) {
        $this->testExtensionDepended = $testExtensionDepended;
    }

    public function getTestExtensionDepended()
    {
        return $this->testExtensionDepended;
    }

    public function up(Schema $schema, QueryBag $queries)
    {
    }
}
