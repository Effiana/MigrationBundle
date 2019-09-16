<?php

namespace Effiana\MigrationBundle\Command;

use Effiana\MigrationBundle\Locator\FixturePathLocatorInterface;
use Effiana\MigrationBundle\Migration\DataFixturesExecutorInterface;
use Effiana\MigrationBundle\Migration\Loader\DataFixturesLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This command load fixtures
 *
 * @package Effiana\MigrationBundle\Command
 */
class LoadDataFixturesCommand extends Command
{
    public const MAIN_FIXTURES_TYPE = DataFixturesExecutorInterface::MAIN_FIXTURES;
    public const DEMO_FIXTURES_TYPE = DataFixturesExecutorInterface::DEMO_FIXTURES;

    /** @var string */
    protected static $defaultName = 'effiana:migration:data:load';
    /** @var KernelInterface */
    protected $kernel;
    /** @var DataFixturesLoader */
    protected $dataFixturesLoader;
    /** @var DataFixturesExecutorInterface */
    protected $dataFixturesExecutor;
    /** @var FixturePathLocatorInterface */
    protected $fixturePathLocator;
    /**
     * @param KernelInterface $kernel
     * @param DataFixturesLoader $dataFixturesLoader
     * @param DataFixturesExecutorInterface $dataFixturesExecutor
     * @param FixturePathLocatorInterface $fixturePathLocator
     */
    public function __construct(
        KernelInterface $kernel,
        DataFixturesLoader $dataFixturesLoader,
        DataFixturesExecutorInterface $dataFixturesExecutor,
        FixturePathLocatorInterface $fixturePathLocator
    ) {
        parent::__construct();
        $this->kernel = $kernel;
        $this->dataFixturesLoader = $dataFixturesLoader;
        $this->dataFixturesExecutor = $dataFixturesExecutor;
        $this->fixturePathLocator = $fixturePathLocator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Load data fixtures.')
            ->addOption(
                'fixtures-type',
                null,
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'Select fixtures type to be loaded (%s or %s). By default - %s',
                    self::MAIN_FIXTURES_TYPE,
                    self::DEMO_FIXTURES_TYPE,
                    self::MAIN_FIXTURES_TYPE
                ),
                self::MAIN_FIXTURES_TYPE
            )
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Outputs list of fixtures without apply them')
            ->addOption(
                'bundles',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'A list of bundle names to load data from'
            )
            ->addOption(
                'exclude',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'A list of bundle names which fixtures should be skipped'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fixtures = null;
        try {
            $fixtures = $this->getFixtures($input, $output);
        } catch (\RuntimeException $ex) {
            $output->writeln('');
            $output->writeln(sprintf('<error>%s</error>', $ex->getMessage()));

            return $ex->getCode() === 0 ? 1 : $ex->getCode();
        }

        if (!empty($fixtures)) {
            if ($input->getOption('dry-run')) {
                $this->outputFixtures($input, $output, $fixtures);
            } else {
                $this->processFixtures($input, $output, $fixtures);
            }
        }

        return 0;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return array
     * @throws \RuntimeException if loading of data fixtures should be terminated
     */
    protected function getFixtures(InputInterface $input, OutputInterface $output): array
    {
        $includeBundles = $input->getOption('bundles');
        $excludeBundles = $input->getOption('exclude');
        $fixtureRelativePath = $this->getFixtureRelativePath($input);

        /**
         * Symfony 4 App
         */
        $appMigrationPath = str_replace(
            '/',
            DIRECTORY_SEPARATOR,
            $this->kernel->getRootDir() . '/' . $fixtureRelativePath
        );
        if (is_dir($appMigrationPath)) {
            $this->dataFixturesLoader->loadFromDirectory($appMigrationPath);
        }

        /** @var BundleInterface[] $bundles */
        $bundles = $this->kernel->getBundles();
        foreach ($bundles as $bundle) {
            if (!empty($includeBundles) && !in_array($bundle->getName(), $includeBundles, true)) {
                continue;
            }
            if (!empty($excludeBundles) && in_array($bundle->getName(), $excludeBundles, true)) {
                continue;
            }
            $path = $bundle->getPath() . $fixtureRelativePath;
            if (is_dir($path)) {
                $this->dataFixturesLoader->loadFromDirectory($path);
            }
        }

        return $this->dataFixturesLoader->getFixtures();
    }

    /**
     * Output list of fixtures
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $fixtures
     */
    protected function outputFixtures(InputInterface $input, OutputInterface $output, $fixtures): void
    {
        $output->writeln(
            sprintf(
                'List of "%s" data fixtures ...',
                $this->getTypeOfFixtures($input)
            )
        );
        foreach ($fixtures as $fixture) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', get_class($fixture)));
        }
    }

    /**
     * Process fixtures
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $fixtures
     */
    protected function processFixtures(InputInterface $input, OutputInterface $output, $fixtures): void
    {
        $output->writeln(
            sprintf(
                'Loading "%s" data fixtures ...',
                $this->getTypeOfFixtures($input)
            )
        );

        $this->executeFixtures($output, $fixtures, $this->getTypeOfFixtures($input));
    }

    /**
     * @param OutputInterface $output
     * @param array           $fixtures
     * @param string          $fixturesType
     */
    protected function executeFixtures(OutputInterface $output, $fixtures, $fixturesType): void
    {
        $this->dataFixturesExecutor->setLogger(
            static function ($message) use ($output) {
                $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
            }
        );
        $this->dataFixturesExecutor->execute($fixtures, $fixturesType);
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getTypeOfFixtures(InputInterface $input): string
    {
        return $input->getOption('fixtures-type');
    }

    /**
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getFixtureRelativePath(InputInterface $input): string
    {
        $fixtureType         = (string)$this->getTypeOfFixtures($input);
        $fixtureRelativePath = $this->fixturePathLocator->getPath($fixtureType);

        return str_replace('/', DIRECTORY_SEPARATOR, sprintf('/%s', $fixtureRelativePath));
    }
}
