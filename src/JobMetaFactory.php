<?php

namespace Bnza\JobManagerBundle;

use InvalidArgumentException;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity as JobEntity;

class JobMetaFactory
{
    /**
     * @var array<string, JobFactoryInterface>
     */
    private array $factories;

    public function __construct(array $factories)
    {
        $this->factories = [];

        foreach ($factories as $class => $factory) {
            if (!is_string($class)) {
                throw new InvalidArgumentException(
                    sprintf("Factory map key must be a string: %s given", gettype($class))
                );
            }
            if (!isset(class_implements($class)[JobInterface::class])) {
                throw new InvalidArgumentException(
                    sprintf(
                        "Factory map key must be fully qualified class name which implements \"%s\": \"%s\" given",
                        JobInterface::class,
                        $class
                    )
                );
            }
            if (!$factory instanceof JobFactoryInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        "Factory map value must implements \"%s\"",
                        JobFactoryInterface::class
                    )
                );
            }
        }

        $this->factories = $factories;
    }

    public function createFromEntity(JobEntity $entity): JobInterface
    {
        if (!isset($this->factories[$entity->getClass()])) {
            throw new InvalidArgumentException("No factory defined for class {$entity->getClass()}");
        }
        $factory = $this->factories[$entity->getClass()];

        return $factory->createFromEntity($entity);
    }
}
