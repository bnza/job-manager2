<?php

namespace Bnza\JobManagerBundle\DependencyInjection\Compiler;

use Bnza\JobManagerBundle\WorkUnitFactoryServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WorkUnitFactoryServiceLocatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $factoryLocatorDefinition = $container->getDefinition(WorkUnitFactoryServiceLocator::class);


        $taggedServices = $container->findTaggedServiceIds('bnza_job_manager.work_unit_factory');
        $services = [];

        foreach ($taggedServices as $id => $tags) {
            $def = $container->getDefinition($id);
            if ($def->isAbstract()) {
                continue;
            }
            $services[$id] = new Reference($id);
        }

        $factoryLocatorDefinition->setArgument('$factories', $services);
    }
}
