<?php

namespace Bnza\JobManagerBundle\DependencyInjection\Compiler;

use Bnza\JobManagerBundle\JobInterface;
use Bnza\JobManagerBundle\TaskInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddTagsToAutoconfiguredServicesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
//        foreach ($container->getDefinitions() as $id => $definition) {
//            // Check if the service is autoconfigured (no explicit ID)
//            if (str_contains($id, '\\') || !str_contains($id, '.')) {
//                // Check if the service implements a specific interface or extends a class
//                if ($definition->getClass() && is_subclass_of($definition->getClass(), JobInterface::class)) {
//                    $definition->addTag('bnza_job_manager.job');
//                    $definition->setShared(false);
//                }
//                if ($definition->getClass() && is_subclass_of($definition->getClass(), TaskInterface::class)) {
//                    $definition->addTag('bnza_job_manager.task');
//                    $definition->setShared(false);
//                }
//            }
//        }
    }
}
