<?php

namespace Effiana\MigrationBundle;

use Effiana\MigrationBundle\DependencyInjection\Compiler\MigrationExtensionPass;
use Effiana\MigrationBundle\DependencyInjection\Compiler\ServiceContainerRealRefPass;
use Effiana\MigrationBundle\DependencyInjection\Compiler\ServiceContainerWeakRefPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EffianaMigrationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MigrationExtensionPass());
        $container->addCompilerPass(new ServiceContainerWeakRefPass(), PassConfig::TYPE_BEFORE_REMOVING, -32);
        $container->addCompilerPass(new ServiceContainerRealRefPass(), PassConfig::TYPE_AFTER_REMOVING);
    }
}
