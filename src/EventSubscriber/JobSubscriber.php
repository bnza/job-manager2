<?php

namespace Bnza\JobManagerBundle\EventSubscriber;

use Bnza\JobManagerBundle\CacheHelper;
use Bnza\JobManagerBundle\Event\WorkUnitEvent;
use Bnza\JobManagerBundle\Exception\JobCancelledException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JobSubscriber implements EventSubscriberInterface
{
    private ObjectManager $entityManager;

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly string $emName,
        private readonly CacheHelper $cacheHelper
    ) {
        $this->entityManager = $this->registry->getManager($this->emName);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkUnitEvent::PARAMETERS_SET => 'persistAndFlush',
            WorkUnitEvent::STEP_STARTED => 'onStepStarted',
            WorkUnitEvent::CANCELLED => 'persistAndFlush',
            WorkUnitEvent::ERROR => 'persistAndFlush',
        ];
    }

    public function persistAndFlush(WorkUnitEvent $event): void
    {
        $entity = $event->getWorkUnit()->toEntity();
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function onStepStarted(WorkUnitEvent $event): void
    {
        $workUnit = $event->getWorkUnit();
        $cancelled = $this->cacheHelper->get($workUnit->getId(), CacheHelper::KEY_IS_CANCELLED);
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
