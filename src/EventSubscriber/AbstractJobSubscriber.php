<?php

namespace Bnza\JobManagerBundle\EventSubscriber;

use Bnza\JobManagerBundle\CacheHelper;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractJobSubscriber implements EventSubscriberInterface
{

    protected ObjectManager $entityManager;
    protected ObjectRepository $repository;

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly string $emName,
    ) {
        $this->entityManager = $this->registry->getManager($this->emName);
        $this->repository = $this->entityManager->getRepository(WorkUnitEntity::class);
    }

    protected function getEntityManager(): ObjectManager
    {
        if (!$this->entityManager->isOpen()) {
            $this->entityManager = $this->registry->resetManager($this->emName);
        }

        return $this->entityManager;
    }
}
