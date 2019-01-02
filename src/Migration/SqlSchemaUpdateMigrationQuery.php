<?php

namespace Effiana\MigrationBundle\Migration;

class SqlSchemaUpdateMigrationQuery extends SqlMigrationQuery implements SchemaUpdateQuery
{
    /**
     * {@inheritdoc}
     */
    public function isUpdateRequired()
    {
        return true;
    }
}
