<?php

namespace Effiana\MigrationBundle\Tests\Unit\Event;

use Effiana\MigrationBundle\Event\PreMigrationEvent;

class PreMigrationEventTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PreMigrationEvent
     */
    protected $preMigrationEvent;

    protected function setUp()
    {
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->preMigrationEvent = new PreMigrationEvent($connection);
    }

    public function testLoadedVersions()
    {
        $this->preMigrationEvent->setLoadedVersion('testBundle', 'v1_0');
        $this->assertEquals(['testBundle' => 'v1_0'], $this->preMigrationEvent->getLoadedVersions());
        $this->assertEquals('v1_0', $this->preMigrationEvent->getLoadedVersion('testBundle'));
        $this->assertNull($this->preMigrationEvent->getLoadedVersion('nonLoggedBundle'));
    }
}
