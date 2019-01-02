<?php

namespace Effiana\MigrationBundle\Migration\Extension;

interface DataStorageExtensionAwareInterface
{
    /**
     * @param DataStorageExtension $dataStorageExtension
     */
    public function setDataStorageExtension(DataStorageExtension $dataStorageExtension);
}
