<?php

namespace Bnza\JobManagerBundle\DependencyInjection\Compiler;

use Bnza\JobManagerBundle\WorkUnitDefinitionServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WorkUnitDefinitionsLocatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(WorkUnitDefinitionServiceLocator::class);
        $taggedServices = $container->findTaggedServiceIds('bnza_job_manager.work_unit_definition');
        $services = [];

        foreach ($taggedServices as $id => $tags) {
            $def = $container->getDefinition($id);
            if ($def->isAbstract()) {
                continue;
            }
            $services[$def->getArgument('$service')] = new Reference($id);
        }

        $definition->setArgument('$definitions', $services);
    }
}
