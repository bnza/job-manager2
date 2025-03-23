<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\DependencyInjection\Compiler\JobCacheHelperCompilerClass;
use Bnza\JobManagerBundle\DependencyInjection\Compiler\JobEntityManagerCompilerClass;
use Bnza\JobManagerBundle\DependencyInjection\Compiler\WorkUnitDefinitionsLocatorCompilerPass;
use Bnza\JobManagerBundle\DependencyInjection\Compiler\WorkUnitFactoryServiceLocatorCompilerPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class BnzaJobManagerBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition
            ->rootNode()
            ->children()
            ->stringNode('em_name')->defaultValue('bnza_job_manager')->end()
            ->stringNode('cache_pool_name')->defaultValue('redis.cache')->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->parameters()
            ->set('bnza_job_manager.em_name', $config['em_name'])
            ->set('bnza_job_manager.cache_pool_name', $config['cache_pool_name']);
        $container->import('../config/services.xml');
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
//        $container->addCompilerPass(new JobServicesIdLocatorCompilerPass());
        $container->addCompilerPass(new JobCacheHelperCompilerClass());
        $container->addCompilerPass(new JobEntityManagerCompilerClass());
        $container->addCompilerPass(new WorkUnitDefinitionsLocatorCompilerPass());
        $container->addCompilerPass(new WorkUnitFactoryServiceLocatorCompilerPass());
    }
}
