<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\DependencyInjection\Compiler\JobServicesIdLocatorCompilerPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class BnzaJobManagerBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()->children()->stringNode('em_name')->defaultValue('bnza_job_manager')->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.xml');
        $container->parameters()
            ->set('bnza_job_manager.em_name', $config['em_name']);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
//        $container->addCompilerPass(new AddTagsToAutoconfiguredServicesPass());
        $container->addCompilerPass(new JobServicesIdLocatorCompilerPass());
    }
}
