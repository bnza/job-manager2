<?php

namespace Bnza\JobManagerBundle\DependencyInjection\Compiler;

use Bnza\JobManagerBundle\JobServicesIdLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class JobEntityManagerCompilerClass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $emName = $container->getParameter('bnza_job_manager.em_name');
        $entityManager = new Reference(sprintf('doctrine.orm.%s_entity_manager', $emName));

        foreach (['Bnza\\JobManagerBundle\\State\\WorkUnitItemProvider'] as $serviceId) {
            $definition = $container->getDefinition($serviceId);
            $definition->setArgument('$entityManager', $entityManager);
        }
    }
}
