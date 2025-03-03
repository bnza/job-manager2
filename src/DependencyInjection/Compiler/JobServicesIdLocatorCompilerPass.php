<?php

namespace Bnza\JobManagerBundle\DependencyInjection\Compiler;

use Bnza\JobManagerBundle\JobServicesIdLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class JobServicesIdLocatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('bnza_job_manager.job_locator');
        $taggedServices = $container->findTaggedServiceIds('bnza_job_manager.job');
        $services = [];

        foreach ($taggedServices as $id => $tags) {
            $def = $container->getDefinition($id);
            if ($def->isAbstract()) {
                continue;
            }
            $services[$id] = new Reference($id);
        }

        $definition->setArgument('$services', $services);
    }
}
