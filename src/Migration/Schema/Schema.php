<?php

namespace Effiana\MigrationBundle\Migration\Schema;

use Doctrine\DBAL\Schema\Schema as BaseSchema;
use Doctrine\DBAL\Schema\Table as BaseTable;

/**
 * The aim of this class is to provide a way extend doctrine Table class
 * To do this just define your table class name in TABLE_CLASS constant in an extended class
 * and override createTableObject if your table class constructor need an additional arguments
 */
class Schema extends BaseSchema
{
    /**
     * Used table class, define TABLE_CLASS constant in an extended class to extend the table class
     * Important: your class must extend Effiana\MigrationBundle\Migration\Schema\Table class
     *            or extend Doctrine\DBAL\Schema\Table class and must have __construct(array $args) method
     */
    public const TABLE_CLASS = BaseTable::class;

    /**
     * Creates an instance of TABLE_CLASS class
     *
     * @param array $args An arguments for TABLE_CLASS class constructor
     *                    An instance of a base table is in 'table' element
     * @return BaseTable
     */
    protected function createTableObject(array $args): BaseTable
    {
        $tableClass = static::TABLE_CLASS;

        return new $tableClass($args);
    }

    /**
     * {@inheritdoc}
     */
    public function createTable($tableName): BaseTable
    {
        parent::createTable($tableName);

        return $this->getTable($tableName);
    }

    /**
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreStart
    protected function _addTable(BaseTable $table): void
    {
        if (static::TABLE_CLASS !== 'Doctrine\DBAL\Schema\Table' && get_class($table) !== static::TABLE_CLASS) {
            $table = $this->createTableObject(['table' => $table]);
        }
        parent::_addTable($table);
    }
    // @codingStandardsIgnoreEnd
}
