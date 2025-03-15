<?php

namespace Bnza\JobManagerBundle\EventSubscriber;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Bnza\JobManagerBundle\Entity\WorkUnitErrorEntity;
use Bnza\JobManagerBundle\Exception\JobExceptionInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

class JobErrorSubscriber extends AbstractJobSubscriber
{

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => 'onWorkMessageFailure',
        ];
    }

    public function onWorkMessageFailure(WorkerMessageFailedEvent $event): void
    {
        $envelope = $event->getEnvelope();
        $throwable = $event->getThrowable();
        $receiverName = $event->getReceiverName();
        $exception = $throwable->getPrevious();
        if ($exception instanceof JobExceptionInterface) {
            $job = $this->getEntityManager()->getRepository(WorkUnitEntity::class)->find($exception->getJobId());
            $previous = $exception->getPrevious();
            $class = $previous ? get_class($previous) : get_class($exception);
            $errorEntity = new WorkUnitErrorEntity()
                ->setWorkUnit($job)
                ->setMessage($exception->getMessage())
                ->setClass($class)
                ->setValues($exception->getValues());
            $this->getEntityManager()->persist($errorEntity);
            $this->getEntityManager()->flush();
        }
    }
}
