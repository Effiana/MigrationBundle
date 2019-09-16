<?php

namespace Effiana\MigrationBundle\Migration\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Effiana\MigrationBundle\Entity\DataFixture;
use Effiana\MigrationBundle\Fixture\LoadedFixtureVersionAwareInterface;
use Effiana\MigrationBundle\Fixture\VersionedFixtureInterface;
use Effiana\MigrationBundle\Migration\Sorter\DataFixturesSorter;
use Effiana\MigrationBundle\Migration\UpdateDataFixturesFixture;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\Loader;

/**
 * Class DataFixturesLoader
 * @package Effiana\MigrationBundle\Migration\Loader
 */
class DataFixturesLoader extends ContainerAwareLoader
{
    /** @var EntityManager */
    protected $em;

    /** @var array */
    protected $loadedFixtures;

    /** @var \ReflectionProperty */
    protected $ref;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        parent::__construct($container);

        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function getFixtures(): array
    {
        $sorter   = new DataFixturesSorter();
        $fixtures = $sorter->sort($this->getAllFixtures());

        // remove already loaded fixtures
        foreach ($fixtures as $key => $fixture) {
            if ($this->isFixtureAlreadyLoaded($fixture)) {
                unset($fixtures[$key]);
            }
        }

        // add a special fixture to mark new fixtures as "loaded"
        if (!empty($fixtures)) {
            $toBeLoadFixtureClassNames = [];
            foreach ($fixtures as $fixture) {
                $version = null;
                if ($fixture instanceof VersionedFixtureInterface) {
                    $version = $fixture->getVersion();
                }
                $toBeLoadFixtureClassNames[get_class($fixture)] = $version;
            }

            $updateFixture = new UpdateDataFixturesFixture();
            $updateFixture->setDataFixtures($toBeLoadFixtureClassNames);
            $fixtures[get_class($updateFixture)] = $updateFixture;
        }

        return $fixtures;
    }

    /**
     * Determines whether the given data fixture is already loaded or not
     *
     * @param object $fixtureObject
     *
     * @return bool
     */
    protected function isFixtureAlreadyLoaded($fixtureObject): bool
    {
        if (!$this->loadedFixtures) {
            $this->loadedFixtures = [];

            $loadedFixtures = $this->em->getRepository('EffianaMigrationBundle:DataFixture')->findAll();
            /** @var DataFixture $fixture */
            foreach ($loadedFixtures as $fixture) {
                $this->loadedFixtures[$fixture->getClassName()] = $fixture->getVersion() ?: '0.0';
            }
        }

        $alreadyLoaded = false;

        if (isset($this->loadedFixtures[get_class($fixtureObject)])) {
            $alreadyLoaded = true;
            $loadedVersion = $this->loadedFixtures[get_class($fixtureObject)];
            if ($fixtureObject instanceof VersionedFixtureInterface
                && version_compare($loadedVersion, $fixtureObject->getVersion()) === -1
            ) {
                if ($fixtureObject instanceof LoadedFixtureVersionAwareInterface) {
                    $fixtureObject->setLoadedVersion($loadedVersion);
                }
                $alreadyLoaded = false;
            }
        }

        return $alreadyLoaded;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    protected function getAllFixtures(): array
    {
        if (!$this->ref) {
            $this->ref = new \ReflectionProperty(Loader::class, 'fixtures');
            $this->ref->setAccessible(true);
        }

        return $this->ref->getValue($this);
    }
}
