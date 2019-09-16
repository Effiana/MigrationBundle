<?php

namespace Effiana\MigrationBundle\Migration;

use Doctrine\ORM\EntityManagerInterface;
use Effiana\MigrationBundle\Event\MigrationDataFixturesEvent;
use Effiana\MigrationBundle\Event\MigrationEvents;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Executes ORM data fixtures.
 */
class DataFixturesExecutor implements DataFixturesExecutorInterface
{
    /** @var EntityManager */
    private $em;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var callable|null */
    private $logger;

    /**
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $fixtures, $fixturesType): void
    {
        $event = new MigrationDataFixturesEvent($this->em, $fixturesType, $this->logger);
        $this->eventDispatcher->dispatch($event, MigrationEvents::DATA_FIXTURES_PRE_LOAD);

        $executor = new ORMExecutor($this->em);
        if (null !== $this->logger) {
            $executor->setLogger($this->logger);
        }
        $executor->execute($fixtures, true);

        $this->eventDispatcher->dispatch($event, MigrationEvents::DATA_FIXTURES_POST_LOAD);
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger($logger): void
    {
        $this->logger = $logger;
    }
}
