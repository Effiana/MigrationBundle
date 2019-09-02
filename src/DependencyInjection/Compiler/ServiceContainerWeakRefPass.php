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

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
/**
 * Collects all private services and their aliases to build the service locator for the migration container.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ServiceContainerWeakRefPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('effiana_migration.service_container')) {
            return;
        }
        $privateServices = [];
        $definitions = $container->getDefinitions();
        foreach ($definitions as $id => $definition) {
            if (!$definition->isPublic() && !$definition->getErrors() && !$definition->isAbstract()) {
                $privateServices[$id] = new ServiceClosureArgument(
                    new Reference($id, ContainerBuilder::IGNORE_ON_UNINITIALIZED_REFERENCE)
                );
            }
        }
        $aliases = $container->getAliases();
        foreach ($aliases as $id => $alias) {
            if (!$alias->isPublic()) {
                while (isset($aliases[$target = (string) $alias])) {
                    $alias = $aliases[$target];
                }
                if (isset($definitions[$target]) &&
                    !$definitions[$target]->getErrors() &&
                    !$definitions[$target]->isAbstract()
                ) {
                    $privateServices[$id] = new ServiceClosureArgument(
                        new Reference($target, ContainerBuilder::IGNORE_ON_UNINITIALIZED_REFERENCE)
                    );
                }
            }
        }
        if ($privateServices) {
            $definition = $definitions[(string) $definitions['effiana_migration.service_container']->getArgument(2)];
            $definition->replaceArgument(0, $privateServices);
        }
    }
}