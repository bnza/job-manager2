<?php

namespace Bnza\JobManagerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class JobCacheHelperCompilerClass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $cachePoolServiceId = $container->getParameter('bnza_job_manager.cache_pool_name');
        $cachePoolService = new Reference($cachePoolServiceId);

        foreach ([
                     'bnza_job_manager.cache_helper',
                 ] as $serviceId) {
            $definition = $container->getDefinition($serviceId);
            $definition->setArgument('$cache', $cachePoolService);
        }
    }
}
