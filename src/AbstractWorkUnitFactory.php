<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractWorkUnitFactory implements WorkUnitFactoryInterface
{
    public function __construct(
        protected readonly WorkUnitDefinitionInterface $definition,
        protected readonly EventDispatcherInterface $eventDispatcher,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function supports(string $id): bool
    {
        return $this->definition->getService() === $id;
    }

    public function toEntity(): WorkUnitEntity
    {
        // @todo add toEntity() to WorkUnitDefinition
        return new WorkUnitEntity()
            ->setClass($this->definition->getClass())
            ->setService($this->definition->getService())
            ->setDescription($this->definition->getDescription());
    }
}
