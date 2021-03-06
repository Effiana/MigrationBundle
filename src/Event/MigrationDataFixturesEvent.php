<?php

namespace Effiana\MigrationBundle\Event;

use Doctrine\Common\Persistence\ObjectManager;
use Effiana\MigrationBundle\Migration\DataFixturesExecutorInterface;
use Symfony\Contracts\EventDispatcher\Event;

class MigrationDataFixturesEvent extends Event
{
    /** @var ObjectManager */
    private $manager;

    /** @var string */
    private $fixturesType;

    /** @var callable|null */
    private $logger;

    /**
     * @param ObjectManager $manager      The entity manager
     * @param string        $fixturesType The type of data fixtures
     * @param callable|null $logger       The callback for logging messages
     */
    public function __construct(ObjectManager $manager, $fixturesType, $logger = null)
    {
        $this->manager = $manager;
        $this->fixturesType = $fixturesType;
        $this->logger = $logger;
    }

    /**
     * Gets the entity manager.
     *
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        return $this->manager;
    }

    /**
     * Gets the type of data fixtures.
     *
     * @return string
     */
    public function getFixturesType(): string
    {
        return $this->fixturesType;
    }

    /**
     * Adds a message to the logger.
     *
     * @param string $message
     */
    public function log($message): void
    {
        if (null !== $this->logger) {
            $logger = $this->logger;
            $logger($message);
        }
    }

    /**
     * Checks whether this event is raised for data fixtures contain the main data for an application.
     *
     * @return bool
     */
    public function isMainFixtures(): bool
    {
        return DataFixturesExecutorInterface::MAIN_FIXTURES === $this->getFixturesType();
    }

    /**
     * Checks whether this event is raised for data fixtures contain the demo data for an application.
     *
     * @return bool
     */
    public function isDemoFixtures(): bool
    {
        return DataFixturesExecutorInterface::DEMO_FIXTURES === $this->getFixturesType();
    }
}
