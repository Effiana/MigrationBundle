<?php

namespace Effiana\MigrationBundle\Tests\Functional\Locator;

use Effiana\MigrationBundle\Locator\FixturePathLocator;
use Effiana\MigrationBundle\Locator\FixturePathLocatorInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FixturePathLocatorTest extends WebTestCase
{
    /**
     * @var FixturePathLocatorInterface
     */
    private $serviceLocator;

    protected function setUp()
    {

        $this->serviceLocator = new FixturePathLocator();
    }

    public function testGetPathWithDemoType(): void
    {
        $path = $this->serviceLocator->getPath('demo');

        $this->assertEquals($path, 'Migrations/Data/Demo/ORM');
    }

    public function testGetPathWithMainType(): void
    {
        $path = $this->serviceLocator->getPath('main');

        $this->assertEquals($path, 'Migrations/Data/ORM');
    }

    public function testGetPathWithEmptyType(): void
    {
        $path = $this->serviceLocator->getPath('');

        $this->assertEquals($path, 'Migrations/Data/ORM');
    }
}
