<?php

namespace Bnza\JobManagerBundle;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractJobFactory extends AbstractWorkUnitFactory
{
    public function __construct(
        protected readonly array $workUnitFactories,
        WorkUnitDefinitionInterface $definition,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        parent::__construct($definition, $eventDispatcher, $logger);
    }
}
