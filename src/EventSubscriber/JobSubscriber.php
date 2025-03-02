<?php

namespace Bnza\JobManagerBundle\EventSubscriber;

use Bnza\JobManagerBundle\Event\JobEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JobSubscriber implements EventSubscriberInterface
{
    private ObjectManager $entityManager;

    public function __construct(
        private ManagerRegistry $registry,
        private readonly string $emName,
        private ValidatorInterface $validator
    ) {
        $this->entityManager = $this->registry->getManager($this->emName);
    }

    public static function getSubscribedEvents(): array
    {
        return [
//            JobEvent::CREATED => 'onJobCreated',
            JobEvent::PARAMETERS_SET => 'persistAndFlush',
        ];
    }

    public function onJobCreated(JobEvent $event): void
    {
        $job = $event->getJob();
        $entity = $job->toEntity();
        if (is_null($entity->getId())) {
            // Entity ha not been persisted yet
            $violations = $this->validator->validate($entity);
            if (count($violations) > 0) {
                throw new ValidationFailedException('User data validation failed', $violations);
            }
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } else {
            $this->entityManager->refresh($entity);
            $job->setParameters($entity->getParameters(), false);
        }
    }

    public function persistAndFlush(JobEvent $event): void
    {
        $entity = $event->getJob()->toEntity();
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
