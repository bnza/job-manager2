<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity as JobEntity;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Uid\Uuid;

final readonly class JobRunner
{

    private ObjectManager $entityManager;

    public function __construct(
        private ManagerRegistry $registry,
        string $emName,
        private WorkUnitFactoryServiceLocator $locator,
    ) {
        $this->entityManager = $this->registry->getManager($emName);
    }

    public function run(Uuid $id): void
    {
        $entity = $this->entityManager->find(JobEntity::class, $id);
        if (null === $entity) {
            throw new InvalidArgumentException("WorkUnitEntity '$id' not found.");
        }

        if (!is_null($entity->getParent())) {
            throw new RuntimeException("Only root jobs can be run: '$id' is not");
        }

        if (!$entity->getStatus()->isIdle()) {
            throw new RuntimeException("Only idling jobs can be run: '$id' is not");
        }

        $serviceId = $entity->getService();

        $factory = $this->locator->get($serviceId);

        $job = $factory->create();

        $job->configure($entity);
        $job->run();
    }
}
