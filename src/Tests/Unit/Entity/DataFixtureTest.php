<?php

namespace Effiana\MigrationBundle\Tests\Unit\Entity;

use Effiana\MigrationBundle\Entity\DataFixture;

class DataFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DataFixture
     */
    protected $dataFixtureEntity;

    protected function setUp()
    {
        $this->dataFixtureEntity = new DataFixture();
    }

    public function testDataFixtureEntity()
    {
        $this->assertNull($this->dataFixtureEntity->getId());
        $this->assertNull($this->dataFixtureEntity->getClassName());
        $this->dataFixtureEntity->setClassName('testClass');
        $this->assertEquals('testClass', $this->dataFixtureEntity->getClassName());
        $this->assertNull($this->dataFixtureEntity->getLoadedAt());
        $this->dataFixtureEntity->setLoadedAt(new \DateTime('2013-01-01'));
        $this->assertEquals('2013-01-01', $this->dataFixtureEntity->getLoadedAt()->format('Y-m-d'));
    }
}
