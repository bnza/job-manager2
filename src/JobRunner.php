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
        private JobServicesIdLocator $locator,
    ) {
        $this->entityManager = $this->registry->getManager($emName);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
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
        if (!$this->locator->has($serviceId)) {
            throw new RuntimeException("Service \"$serviceId\" not found.");
        }

        $job = $this->locator->get($serviceId);

        if (!($job instanceof JobInterface)) {
            throw new RuntimeException("WorkUnitEntity '$id' must implement JobInterface.");
        }

        $job->configure($entity);
        $job->run();
    }
}
