<?php
/**
 * This file is part of the Effiana package.
 *
 * (c) Effiana, LTD
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Dominik Labudzinski <dominik@labudzinski.com>
 */
declare(strict_types=0);

namespace Effiana\MigrationBundle\DependencyInjection\Compiler;

use Effiana\MigrationBundle\Container\MigrationContainer;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
/**
 * Rebuilds the service locator services with real service references.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ServiceContainerRealRefPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(MigrationContainer::class)) {
            return;
        }
        $migrationContainer = $container->getDefinition(MigrationContainer::class);
        $privateContainer = $container->getDefinition((string) $migrationContainer->getArgument(2));
        $definitions = $container->getDefinitions();
        /** @var ServiceClosureArgument $argument */
        foreach ($privateContainer->getArgument(0) as $id => $argument) {
            $target = (string) $argument->getValues()[0];
            if (isset($definitions[$target])) {
                $argument->setValues([new Reference($target)]);
            }
        }
    }
}