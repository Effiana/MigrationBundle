<?php

namespace Effiana\MigrationBundle\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaConfig;
use Effiana\MigrationBundle\Exception\InvalidNameException;
use Effiana\MigrationBundle\Migration\Schema\SchemaWithNameGenerator;
use Effiana\MigrationBundle\Tools\DbIdentifierNameGenerator;

/**
 * Class MigrationExecutorWithNameGenerator
 * @package Effiana\MigrationBundle\Migration
 */
class MigrationExecutorWithNameGenerator extends MigrationExecutor
{
    /**
     * @var DbIdentifierNameGenerator
     */
    protected $nameGenerator;

    /**
     * @param DbIdentifierNameGenerator $nameGenerator
     */
    public function setNameGenerator(DbIdentifierNameGenerator $nameGenerator): void
    {
        $this->nameGenerator = $nameGenerator;
        if ($this->extensionManager) {
            $this->extensionManager->setNameGenerator($this->nameGenerator);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionManager(MigrationExtensionManager $extensionManager): void
    {
        parent::setExtensionManager($extensionManager);
        if ($this->nameGenerator) {
            $this->extensionManager->setNameGenerator($this->nameGenerator);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createSchemaObject(array $tables = [], array $sequences = [], ?SchemaConfig $schemaConfig = null): Schema
    {
        if ($schemaConfig && $this->nameGenerator) {
            $schemaConfig->setMaxIdentifierLength($this->nameGenerator->getMaxIdentifierSize());
        }

        return new SchemaWithNameGenerator(
            $this->nameGenerator,
            $tables,
            $sequences,
            $schemaConfig
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function checkTableName($tableName, Migration $migration): void
    {
        parent::checkTableName($tableName, $migration);
        if (strlen($tableName) > $this->nameGenerator->getMaxIdentifierSize()) {
            throw new InvalidNameException(
                sprintf(
                    'Max table name length is %s. Please correct "%s" table in "%s" migration',
                    $this->nameGenerator->getMaxIdentifierSize(),
                    $tableName,
                    get_class($migration)
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function checkColumnName($tableName, $columnName, Migration $migration): void
    {
        parent::checkColumnName($tableName, $columnName, $migration);
        if (strlen($columnName) > $this->nameGenerator->getMaxIdentifierSize()) {
            throw new InvalidNameException(
                sprintf(
                    'Max column name length is %s. Please correct "%s:%s" column in "%s" migration',
                    $this->nameGenerator->getMaxIdentifierSize(),
                    $tableName,
                    $columnName,
                    get_class($migration)
                )
            );
        }
    }
}
