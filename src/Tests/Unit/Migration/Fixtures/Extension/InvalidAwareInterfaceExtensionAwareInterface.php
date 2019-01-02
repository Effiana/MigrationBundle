<?php

namespace Effiana\MigrationBundle\Tests\Unit\Migration\Fixtures\Extension;

interface InvalidAwareInterfaceExtensionAwareInterface
{
    /**
     * It is invalid method name. The valid name is setInvalidAwareInterfaceExtension
     *
     * @param InvalidAwareInterfaceExtension $extension
     */
    public function setExtension(InvalidAwareInterfaceExtension $extension);
}
