<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\Job as JobEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\Uid\Uuid;

final readonly class JobRunner
{

    private ObjectManager $entityManager;

    public function __construct(
        private ManagerRegistry $registry,
        string $emName,
        private JobServicesIdLocator $locator,
        private CacheItemPoolInterface $redisCache
    ) {
        $this->entityManager = $this->registry->getManager($emName);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function run(Uuid $id): void
    {
        $rr = (string)$id;
        $entity = $this->entityManager->find(JobEntity::class, $id);
        if (null === $entity) {
            throw new InvalidArgumentException("Job '$id' not found.");
        }

        $serviceId = $entity->getService();
        if (!$this->locator->has($serviceId)) {
            throw new RuntimeException("Service \"$serviceId\" not found.");
        }

        $job = $this->locator->get($serviceId);

        if (!($job instanceof JobInterface)) {
            throw new RuntimeException("Job '$id' must implement JobInterface.");
        }

        $redisKey = sprintf('job.%s', $id->toString());
        $item = $this->redisCache->getItem($redisKey);
        $r = $item->get();

        $job->configure($entity);
        $job->run();
    }
}
