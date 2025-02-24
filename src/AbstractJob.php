<?php

namespace Bnza\JobManagerBundle;


use Bnza\JobManagerBundle\Entity\Job as JobEntity;
use Bnza\JobManagerBundle\Event\JobEvent;
use InvalidArgumentException;
use SplQueue;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Uid\Uuid;

abstract class AbstractJob implements JobInterface
{
    protected readonly Uuid $id;
    protected readonly SplQueue $tasks;
    protected readonly JobEvent $event;
    protected readonly array $parameters;

    abstract public function getName(): string;

    abstract public function getDescription(): string;

    public function __construct(private EventDispatcher $eventDispatcher, Uuid|null $id = null)
    {
        $entity = $this->toEntity();

        if (static::class !== $entity->getClass()) {
            throw new InvalidArgumentException(
                'Job entity "class" property must contain "'.static::class.'". Got: '.$entity->getClass()
            );
        }

        $this->tasks = new SplQueue();
        $this->event = new JobEvent($this);

        $this->eventDispatcher->dispatch($this->event, JobEvent::CREATED);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function toEntity(): JobEntity
    {
        return (new JobEntity($this->id))
            ->setClass(static::class)
            ->setDescription($this->getDescription())
            ->setName($this->getName())
            ->setParameters($this->getParameters());
    }

    public function hasParameter(string $key): bool
    {
        return array_key_exists($key, $this->parameters);
    }

    public function getParameter(string $key): mixed
    {
        if (!$this->hasParameter($key)) {
            throw new InvalidArgumentException("Parameter '$key' not found");
        }

        return $this->parameters[$key];
    }

    public function setParameter(string $key, mixed $value, bool $dispatch = true): static
    {
        $this->parameters[$key] = $value;
        if ($dispatch) {
            $this->eventDispatcher->dispatch($this->event, JobEvent::PARAMETERS_SET);
        }

        return $this;
    }

    public function getParameters(): array
    {
        return array_merge([], $this->parameters);
    }

    public function setParameters(array $parameters, bool $dispatch = true): static
    {
        foreach ($parameters as $name => $value) {
            $this->parameters[$name] = $value;
        }
        if ($dispatch) {
            $this->eventDispatcher->dispatch($this->event, JobEvent::PARAMETERS_SET);
        }

        return $this;
    }

    public function run(): void
    {
        foreach ($this->tasks as $task) {
            $task->run();
        }
    }
}
