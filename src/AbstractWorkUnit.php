<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\Status;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Bnza\JobManagerBundle\Event\WorkUnitEvent;
use Bnza\JobManagerBundle\Exception\JobCancelledException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

abstract class AbstractWorkUnit implements WorkUnitInterface
{
    const string WORK_UNIT_TYPE_JOB = 'job';
    const string WORK_UNIT_TYPE_TASK = 'task';

    protected int $currentStepNumber = -1;

    protected readonly EventDispatcherInterface $eventDispatcher;
    protected WorkUnitEntity $state;

    protected ?string $serviceId = null;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        protected readonly LoggerInterface $logger
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->reset();
    }

    public function reset(): void
    {
        $this->state = new WorkUnitEntity()
            ->setClass(static::class)
            ->setName($this->getName())
            ->setDescription($this->getDescription())
            ->setStatus(new Status());
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function returnParameters(): array;

    public function getId(): ?Uuid
    {
        return $this->state->getId();
    }

    public function configure(WorkUnitEntity $entity): void
    {
        $event = new WorkUnitEvent($this);
        $this->eventDispatcher->dispatch($event, WorkUnitEvent::PRE_CONFIGURE);

        if (is_null($entity->getId())) {
            $this->logger->debug('Pippo: '.$this->state->getService());
            $this->state
                ->setService($entity->getService())
                ->setParent($entity->getParent())
                ->setParameters($entity->getParameters());
        } else {
            $this->state = $entity;
        }

        $this->eventDispatcher->dispatch($event, WorkUnitEvent::POST_CONFIGURE);
    }

    public function rollback(): void
    {
    }

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    public function getEntity(): WorkUnitEntity
    {
        return $this->state;
    }

    public function getCurrentStepNumber(): int
    {
        return $this->currentStepNumber;
    }

    public function getStatusValue(): int
    {
        return $this->state->getStatus()->getValue();
    }

    public function isIdle(): bool
    {
        return $this->state->getStatus()->isIdle();
    }

    public function isRunning(): bool
    {
        return $this->state->getStatus()->isRunning();
    }

    public function isCancelled(): bool
    {
        return $this->state->getStatus()->isCancelled();
    }

    public function isError(): bool
    {
        return $this->state->getStatus()->isError();
    }

    public function isSuccess(): bool
    {
        return $this->state->getStatus()->isSuccess();
    }

    /**
     * @throws JobCancelledException
     */
    public function cancel(): void
    {
        $this->state->getStatus()->cancel();
        $this->eventDispatcher->dispatch(new WorkUnitEvent($this), WorkUnitEvent::CANCELLED);
        throw new JobCancelledException($this->getId());
    }

    protected function getParameter(string $key): mixed
    {
        if (!isset($this->parameters[$key])) {
            throw new InvalidArgumentException(sprintf('Parameter "%s" does not exist.', $key));
        }

        return $this->parameters[$key];
    }
}
