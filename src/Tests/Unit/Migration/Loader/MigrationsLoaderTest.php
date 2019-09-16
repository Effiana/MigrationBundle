<?php

namespace Effiana\MigrationBundle\Tests\Unit\Migration\Loader;

use Effiana\MigrationBundle\Event\MigrationEvents;
use Effiana\MigrationBundle\Event\PreMigrationEvent;
use Effiana\MigrationBundle\Migration\Loader\MigrationsLoader;
use Effiana\MigrationBundle\Migration\MigrationState;
use Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage\Test1Bundle\TestPackageTest1Bundle;
use Effiana\MigrationBundle\Tests\Unit\Fixture\TestPackage\Test2Bundle\TestPackageTest2Bundle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class MigrationsLoaderTest extends \PHPUnit\Framework\TestCase
{
    /** @var MigrationsLoader */
    protected $loader;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $kernel;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $parameterBag;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $container;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $eventDispatcher;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $connection;

    protected function setUp()
    {
        $this->kernel          = $this->getMockBuilder('Symfony\Component\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();
        $this->parameterBag          = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->container       = $this->getMockForAbstractClass(
            'Symfony\Component\DependencyInjection\ContainerInterface'
        );
        $this->eventDispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock();

        $this->connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $this->loader = new MigrationsLoader(
            $this->kernel,
            $this->parameterBag,
            $this->connection,
            $this->container,
            $this->eventDispatcher
        );
    }

    /**
     * @dataProvider getMigrationsProvider
     */
    public function testGetMigrations($bundles, $installed, $expectedMigrationClasses)
    {
        $bundlesList = [];
        /** @var \Symfony\Component\HttpKernel\Bundle\Bundle $bundle */
        foreach ($bundles as $bundle) {
            $bundlesList[$bundle->getName()] = $bundle;
        }

        $this->kernel->expects($this->any())
            ->method('getBundles')
            ->willReturn($bundlesList);

        $this->eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(
                static function ($eventName, $event) use (&$installed) {
                    if (($eventName === MigrationEvents::PRE_UP) && null !== $installed) {
                        foreach ($installed as $val) {
                            /** @var PreMigrationEvent $event */
                            $event->setLoadedVersion($val['bundle'], $val['version']);
                        }
                    }
                }
            );

        $migrations       = $this->loader->getMigrations();
        $migrationClasses = $this->getMigrationClasses($migrations);
        $this->assertEquals($expectedMigrationClasses, $migrationClasses);
    }

    /**
     * @param MigrationState[] $migrations
     *
     * @return string[]
     */
    protected function getMigrationClasses(array $migrations)
    {
        return array_map(
            function ($migration) {
                return get_class($migration->getMigration());
            },
            $migrations
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getMigrationsProvider()
    {
        $testPackage = 'Effiana\\MigrationBundle\\Tests\\Unit\\Fixture\\TestPackage\\';
        $test1Bundle = $testPackage . 'Test1Bundle\\Migrations\\Schema';
        $test2Bundle = $testPackage . 'Test2Bundle\\Migrations\\Schema';

        return [
            [
                [new TestPackageTest1Bundle(), new TestPackageTest2Bundle()],
                null,
                [
                    $test1Bundle . '\Test1BundleInstallation',
                    $test1Bundle . '\v1_1\Test1BundleMigration11',
                    $test2Bundle . '\v1_0\Test2BundleMigration10',
                    $test2Bundle . '\v1_0\Test2BundleMigration11',
                    $test2Bundle . '\v1_1\Test2BundleMigration12',
                    $test2Bundle . '\v1_1\Test2BundleMigration11',
                    'Effiana\MigrationBundle\Migration\UpdateBundleVersionMigration',
                ]
            ],
            [
                [new TestPackageTest2Bundle(), new TestPackageTest1Bundle()],
                null,
                [
                    $test2Bundle . '\v1_0\Test2BundleMigration10',
                    $test2Bundle . '\v1_0\Test2BundleMigration11',
                    $test2Bundle . '\v1_1\Test2BundleMigration12',
                    $test2Bundle . '\v1_1\Test2BundleMigration11',
                    $test1Bundle . '\Test1BundleInstallation',
                    'Effiana\MigrationBundle\Migration\UpdateBundleVersionMigration',
                ]
            ],
            [
                [new TestPackageTest1Bundle(), new TestPackageTest2Bundle()],
                [],
                [
                    $test1Bundle . '\Test1BundleInstallation',
                    $test1Bundle . '\v1_1\Test1BundleMigration11',
                    $test2Bundle . '\v1_0\Test2BundleMigration10',
                    $test2Bundle . '\v1_0\Test2BundleMigration11',
                    $test2Bundle . '\v1_1\Test2BundleMigration12',
                    'Effiana\MigrationBundle\Migration\UpdateBundleVersionMigration',
                ]
            ],
            [
                [new TestPackageTest1Bundle(), new TestPackageTest2Bundle()],
                [
                    ['bundle' => 'TestPackageTest1Bundle', 'version' => null],
                ],
                [
                    $test1Bundle . '\Test1BundleInstallation',
                    $test1Bundle . '\v1_1\Test1BundleMigration11',
                    $test2Bundle . '\v1_0\Test2BundleMigration10',
                    $test2Bundle . '\v1_0\Test2BundleMigration11',
                    $test2Bundle . '\v1_1\Test2BundleMigration12',
                    'Effiana\MigrationBundle\Migration\UpdateBundleVersionMigration',
                ]
            ],
            [
                [new TestPackageTest1Bundle(), new TestPackageTest2Bundle()],
                [
                    ['bundle' => 'TestPackageTest1Bundle', 'version' => 'v1_0'],
                ],
                [
                    $test1Bundle . '\v1_1\Test1BundleMigration11',
                    $test2Bundle . '\v1_0\Test2BundleMigration10',
                    $test2Bundle . '\v1_0\Test2BundleMigration11',
                    $test2Bundle . '\v1_1\Test2BundleMigration12',
                    $test2Bundle . '\v1_1\Test2BundleMigration11',
                    'Effiana\MigrationBundle\Migration\UpdateBundleVersionMigration',
                ]
            ],
            [
                [new TestPackageTest1Bundle(), new TestPackageTest2Bundle()],
                [
                    ['bundle' => 'TestPackageTest1Bundle', 'version' => 'v1_0'],
                    ['bundle' => 'TestPackageTest2Bundle', 'version' => 'v1_0'],
                ],
                [
                    $test1Bundle . '\v1_1\Test1BundleMigration11',
                    $test2Bundle . '\v1_1\Test2BundleMigration12',
                    $test2Bundle . '\v1_1\Test2BundleMigration11',
                    'Effiana\MigrationBundle\Migration\UpdateBundleVersionMigration',
                ]
            ],
            [
                [new TestPackageTest1Bundle(), new TestPackageTest2Bundle()],
                [
                    ['bundle' => 'TestPackageTest1Bundle', 'version' => 'v1_1'],
                    ['bundle' => 'TestPackageTest2Bundle', 'version' => 'v1_1'],
                ],
                [
                    'Effiana\MigrationBundle\Migration\UpdateBundleVersionMigration',
                ]
            ],
        ];
    }
}
