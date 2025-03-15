<?php

namespace Bnza\JobManagerBundle\EventSubscriber;

use Bnza\JobManagerBundle\CacheHelper;
use Bnza\JobManagerBundle\Entity\Status;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Bnza\JobManagerBundle\Event\WorkUnitEvent;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Uid\Uuid;

class JobSubscriber implements EventSubscriberInterface
{
    private ObjectManager $entityManager;
    private ObjectRepository $repository;

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly string $emName,
        private readonly CacheHelper $cacheHelper
    ) {
        $this->entityManager = $this->registry->getManager($this->emName);
        $this->repository = $this->entityManager->getRepository(WorkUnitEntity::class);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkUnitEvent::POST_CONFIGURE => 'persistAndFlush',
            WorkUnitEvent::STEP_STARTED => 'onStepStarted',
            WorkUnitEvent::STARTED => 'persistAndFlush',
            WorkUnitEvent::SUCCESS => 'persistAndFlush',
            WorkUnitEvent::CANCELLED => 'persistAndFlush',
            WorkUnitEvent::ERROR => 'persistAndFlush',
            WorkUnitEvent::TERMINATED => 'persistAndFlush',
        ];
    }

    private function getEntityManager(): ObjectManager
    {
        if (!$this->entityManager->isOpen()) {
            $this->entityManager = $this->registry->resetManager($this->emName);
        }

        return $this->entityManager;
    }

    public function persistAndFlush(WorkUnitEvent $event): void
    {
        $entity = $event->getWorkUnit()->getEntity();
        $this->getEntityManager()->persist($entity);
        $this->entityManager->flush();
        $this->cacheHelper->set(
            $event->getWorkUnit()->getId(),
            CacheHelper::KEY_STATUS,
            $event->getWorkUnit()->getStatusValue(),
        );
    }

    public function onStepStarted(WorkUnitEvent $event): void
    {
        $workUnit = $event->getWorkUnit();
        $cachedStatus = $this->cacheHelper->get($workUnit->getId(), CacheHelper::KEY_STATUS);
        $cancelled = $cachedStatus && Status::is($cachedStatus, Status::CANCELLED);
        if ($cancelled === true) {
            $workUnit->cancel();
        }
        $this->cacheHelper->set(
            $workUnit->getId(),
            CacheHelper::KEY_CURRENT_STEP_NUMBER,
            $workUnit->getCurrentStepNumber()
        );
    }
}
