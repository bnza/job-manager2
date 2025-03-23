<?php

namespace Bnza\JobManagerBundle;

use ArrayObject;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use InvalidArgumentException;

class WorkUnitFactoryServiceLocator
{
    private ArrayObject $services;

    public function __construct(
        /** @var array<string, WorkUnitFactoryInterface> $factories */ array $factories,
        private readonly WorkUnitDefinitionServiceLocator $workUnitServiceDefinitionLocator
    ) {
        $this->services = new ArrayObject();
        foreach ($factories as $id => $factory) {
            $workUnitId = $factory->toEntity()->getService();
            if (!$this->workUnitServiceDefinitionLocator->has($workUnitId)) {
                throw new InvalidArgumentException(sprintf('Service "%s" does not exist.', $id));
            }

            if (!($factory instanceof WorkUnitFactoryInterface)) {
                throw new InvalidArgumentException(
                    sprintf(
                        '"%s" must implement WorkUnitFactoryInterface: %s given.',
                        $id,
                        is_object($factory) ? get_class($factory) : gettype($factory)
                    )
                );
            }
            $this->services->offsetSet($id, $factory);
        }
    }

    public function get(string $id): WorkUnitFactoryInterface
    {
        $definition = new WorkUnitDefinition('', $id, '');

        return $this->getFactoryByWorkUnit($definition);
    }

    public function has(string $id): bool
    {
        return $this->services->offsetExists($id);
    }

    public function getFactoryByWorkUnit(WorkUnitDefinition $workUnit): WorkUnitFactoryInterface
    {
        foreach ($this->services as $id => $service) {
            if ($service->supports($workUnit->getService())) {
                return $service;
            }
        }
        throw new InvalidArgumentException("Unsupported service id: '{$workUnit->getService()}'.");
    }

    public function getWorkUnitDefinition($id): WorkUnitDefinitionInterface
    {
        if (!$this->workUnitServiceDefinitionLocator->has($id)) {
            throw new InvalidArgumentException(sprintf('Service "%s" does not exist.', $id));
        }

        return $this->workUnitServiceDefinitionLocator->get($id);
    }


}
