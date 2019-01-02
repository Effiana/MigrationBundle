<?php

namespace Effiana\MigrationBundle\Tests\Unit\Migration\Loader;

use Effiana\MigrationBundle\Entity\DataFixture;
use Effiana\MigrationBundle\Migration\Loader\DataFixturesLoader;
use Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage\Test1Bundle\TestPackageTest1Bundle;
use Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage\Test2Bundle\TestPackageTest2Bundle;
use Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage\Test3Bundle\TestPackageTest3Bundle;

class DataFixturesLoaderTest extends \PHPUnit\Framework\TestCase
{
    /** @var DataFixturesLoader */
    protected $loader;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $em;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $container;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $fixtureRepo;

    protected function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->fixtureRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()->getMock();
        $this->em          = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $this->em->expects($this->any())->method('getRepository')->with('EffianaMigrationBundle:DataFixture')
            ->willReturn($this->fixtureRepo);

        $this->loader = new DataFixturesLoader($this->em, $this->container);
    }

    /**
     * @dataProvider getFixturesProvider
     */
    public function testGetFixtures($bundles, $loadedDataFixtureClasses, $expectedFixtureClasses)
    {
        /** @var \Symfony\Component\HttpKernel\Bundle\Bundle $bundle */
        foreach ($bundles as $bundle) {
            $this->loader->loadFromDirectory(
                $bundle->getPath() . '/Migrations/Data/ORM'
            );
        }

        $loadedDataFixtures = [];
        foreach ($loadedDataFixtureClasses as $className => $version) {
            $loadedDataFixtures[] = $this->createDataFixture($className, $version);
        }

        $this->fixtureRepo->expects($this->any())->method('findAll')->willReturn($loadedDataFixtures);

        $fixtures       = $this->loader->getFixtures();
        $fixtureClasses = $this->getFixturesClasses($fixtures);
        $this->assertEquals($expectedFixtureClasses, $fixtureClasses);
    }

    protected function getFixturesClasses(array $fixtures)
    {
        $result = [];
        foreach ($fixtures as $fixture) {
            $result[] = get_class($fixture);
        }

        return $result;
    }

    /**
     * @param string $className
     * @param string $version
     *
     * @return DataFixture
     */
    protected function createDataFixture($className, $version)
    {
        $result = new DataFixture();
        $result->setClassName($className);
        $result->setVersion($version);

        return $result;
    }

    public function getFixturesProvider()
    {
        $test1BundleNamespace = 'Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage\Test1Bundle';
        $test2BundleNamespace = 'Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage\Test2Bundle';
        $test3BundleNamespace = 'Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage\Test3Bundle';

        return [
            [
                [],
                [],
                []
            ],
            [
                [new TestPackageTest1Bundle()],
                [],
                [
                    $test1BundleNamespace . '\Migrations\Data\ORM\LoadTest1BundleData',
                    'Effiana\MigrationBundle\Migration\UpdateDataFixturesFixture',
                ]
            ],
            [
                [new TestPackageTest1Bundle()],
                [
                    $test1BundleNamespace . '\Migrations\Data\ORM\LoadTest1BundleData' => ''
                ],
                []
            ],
            [
                [new TestPackageTest2Bundle()],
                [],
                [
                    $test1BundleNamespace . '\Migrations\Data\ORM\LoadTest1BundleData',
                    $test2BundleNamespace . '\Migrations\Data\ORM\LoadTest2BundleData',
                    'Effiana\MigrationBundle\Migration\UpdateDataFixturesFixture',
                ]
            ],
            [
                [new TestPackageTest2Bundle()],
                [
                    $test1BundleNamespace . '\Migrations\Data\ORM\LoadTest1BundleData' => ''
                ],
                [
                    $test2BundleNamespace . '\Migrations\Data\ORM\LoadTest2BundleData',
                    'Effiana\MigrationBundle\Migration\UpdateDataFixturesFixture',
                ]
            ],
            [
                [new TestPackageTest2Bundle(), new TestPackageTest1Bundle()],
                [],
                [
                    $test1BundleNamespace . '\Migrations\Data\ORM\LoadTest1BundleData',
                    $test2BundleNamespace . '\Migrations\Data\ORM\LoadTest2BundleData',
                    'Effiana\MigrationBundle\Migration\UpdateDataFixturesFixture',
                ]
            ],
            [
                [new TestPackageTest2Bundle(), new TestPackageTest1Bundle()],
                [
                    $test2BundleNamespace . '\Migrations\Data\ORM\LoadTest2BundleData' => ''
                ],
                [
                    $test1BundleNamespace . '\Migrations\Data\ORM\LoadTest1BundleData',
                    'Effiana\MigrationBundle\Migration\UpdateDataFixturesFixture',
                ]
            ],
            [
                [new TestPackageTest3Bundle()],
                [],
                [
                    $test3BundleNamespace . '\Migrations\Data\ORM\LoadTest3BundleData1',
                    $test3BundleNamespace . '\Migrations\Data\ORM\LoadTest3BundleData2',
                    'Effiana\MigrationBundle\Migration\UpdateDataFixturesFixture',
                ]
            ],
            [
                [new TestPackageTest3Bundle()],
                [
                    $test3BundleNamespace . '\Migrations\Data\ORM\LoadTest3BundleData1' => '1.0',
                    $test3BundleNamespace . '\Migrations\Data\ORM\LoadTest3BundleData2' => '0.0'
                ],
                [
                    $test3BundleNamespace . '\Migrations\Data\ORM\LoadTest3BundleData2',
                    'Effiana\MigrationBundle\Migration\UpdateDataFixturesFixture',
                ]
            ],
            [
                [new TestPackageTest3Bundle()],
                [
                    $test3BundleNamespace . '\Migrations\Data\ORM\LoadTest3BundleData1' => '2.0',
                    $test3BundleNamespace . '\Migrations\Data\ORM\LoadTest3BundleData2' => '1.0'
                ],
                []
            ]
        ];
    }
}
