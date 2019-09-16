<?php

namespace Effiana\MigrationBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Effiana\MigrationBundle\Tools\SchemaDumper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DumpMigrationsCommand
 * @package Effiana\MigrationBundle\Command
 */
class DumpMigrationsCommand extends Command
{
    /**
     * @var array
     */
    protected $allowedTables = [];

    /**
     * @var array
     */
    protected $extendedFieldOptions = [];

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $version;
    /**
     * @var SchemaDumper
     */
    private $schemaDumper;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * DumpMigrationsCommand constructor.
     * @param SchemaDumper $schemaDumper
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SchemaDumper $schemaDumper, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->schemaDumper = $schemaDumper;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('effiana:migration:dump')
            ->addOption('plain-sql', null, InputOption::VALUE_NONE, 'Out schema as plain sql queries')
            ->addOption(
                'bundle',
                null,
                InputOption::VALUE_OPTIONAL,
                'Bundle name for which migration wll be generated'
            )
            ->addOption(
                'migration-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'Migration version',
                'v1_0'
            )
            ->setDescription('Dump existing database structure.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->version = $input->getOption('migration-version');
        $this->initializeBundleRestrictions($input->getOption('bundle'));
        $this->initializeMetadataInformation();
        /** @var Schema $schema */
        $schema = $this->entityManager->getConnection()->getSchemaManager()->createSchema();

        if ($input->getOption('plain-sql')) {
            /** @var Connection $connection */
            $connection = $this->entityManager->getConnection();
            $sqls = $schema->toSql($connection->getDatabasePlatform());
            foreach ($sqls as $sql) {
                $output->writeln($sql . ';');
            }
        } else {
            $this->dumpPhpSchema($schema, $output);
        }
    }

    /**
     * @param string $bundle
     */
    protected function initializeBundleRestrictions($bundle): void
    {
        if ($bundle) {
            $bundles = $this->getContainer()->getParameter('kernel.bundles');
            if (!array_key_exists($bundle, $bundles)) {
                throw new \InvalidArgumentException(
                    sprintf('Bundle "%s" is not a known bundle', $bundle)
                );
            }
            $this->namespace = str_replace($bundle, 'Entity', $bundles[$bundle]);
            $this->className = $bundle . 'Installer';
        }
    }

    /**
     * Process metadata information.
     */
    protected function initializeMetadataInformation(): void
    {
        /** @var ClassMetadata[] $allMetadata */
        $allMetadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        array_walk(
            $allMetadata,
            function (ClassMetadata $entityMetadata) {
                if ($this->namespace && $entityMetadata->namespace === $this->namespace) {
                    $this->allowedTables[$entityMetadata->getTableName()] = true;
                    foreach ($entityMetadata->getAssociationMappings() as $associationMappingInfo) {
                        if (!empty($associationMappingInfo['joinTable'])) {
                            $joinTableName = $associationMappingInfo['joinTable']['name'];
                            $this->allowedTables[$joinTableName] = true;
                        }
                    }
                }
            }
        );
    }

    /**
     * @param Schema          $schema
     * @param OutputInterface $output
     */
    protected function dumpPhpSchema(Schema $schema, OutputInterface $output): void
    {
        $schema->visit($this->schemaDumper);

        $output->writeln(
            $this->schemaDumper->dump(
                $this->allowedTables,
                $this->namespace,
                $this->className,
                $this->version,
                $this->extendedFieldOptions
            )
        );
    }
}
