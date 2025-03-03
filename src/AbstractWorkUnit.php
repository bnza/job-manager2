<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\Job as JobEntity;
use Bnza\JobManagerBundle\Event\WorkUnitEvent;
use Bnza\JobManagerBundle\Exception\JobCancelledException;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

abstract class AbstractWorkUnit implements WorkUnitInterface
{
    const WORK_UNIT_TYPE_JOB = 'job';
    const WORK_UNIT_TYPE_TASK = 'task';

    private ?Uuid $id = null;

    protected ?JobInterface $parent = null;

    protected array $parameters;

    protected int $currentStepNumber = -1;

    protected readonly EventDispatcherInterface $eventDispatcher;
    private readonly Status $status;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->status = new Status();
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array $params
     * @return void
     * @throws InvalidArgumentException
     */
    abstract protected function validateParameters(array $params): void;

    /**
     * @return array<string, mixed>
     */
    abstract protected function returnParameters(): array;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function configure(JobEntity $entity, ?JobInterface $parent = null): void
    {
        $event = new WorkUnitEvent($this);
        $this->eventDispatcher->dispatch($event, WorkUnitEvent::PRE_CONFIGURE);
        if (!is_null($this->id)) {
            throw new LogicException('WorkUnit has already been configured.');
        }
        $this->id = $entity->getId();
        $this->validateParameters($entity->getParameters());
        $this->parameters = $entity->getParameters();
        if ($parent) {
            $this->parent = $parent;
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

    public function toEntity(): JobEntity
    {
        return new JobEntity($this->id)
            ->setClass(static::class)
            ->setName($this->getName())
            ->setDescription($this->getDescription())
            ->setStatus($this->status);
    }

    public function getCurrentStepNumber(): int
    {
        return $this->currentStepNumber;
    }

    public function isIdle(): bool
    {
        return $this->status->isIdle();
    }

    public function isRunning(): bool
    {
        return $this->status->isRunning();
    }

    public function isCancelled(): bool
    {
        return $this->status->isCancelled();
    }

    public function isError(): bool
    {
        return $this->status->isError();
    }

    public function isSuccess(): bool
    {
        return $this->status->isSuccess();
    }

    public function cancel(): void
    {
        $this->status->cancel();
        $this->eventDispatcher->dispatch(new WorkUnitEvent($this), WorkUnitEvent::CANCELLED);
        throw new JobCancelledException();
    }
}
