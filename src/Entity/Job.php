<?php

namespace Bnza\JobManagerBundle\Entity;

use Bnza\JobManagerBundle\Status;
use Symfony\Component\Uid\Uuid;

class Job
{
    private string $name;
    private string $class;
    private string $description;
    private array $parameters;
    private int $stepsCount;
    private Status $status;
    private float $startedAt;
    private float $terminatedAt;

    public function __construct(private Uuid $id)
    {
    }

    public function getId(): Uuid|null
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Job
    {
        $this->name = $name;

        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): Job
    {
        $this->class = $class;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Job
    {
        $this->description = $description;

        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): Job
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getStepsCount(): int
    {
        return $this->stepsCount;
    }

    public function setStepsCount(int $stepsCount): Job
    {
        $this->stepsCount = $stepsCount;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): Job
    {
        $this->status = $status;

        return $this;
    }

    public function getStartedAt(): float
    {
        return $this->startedAt;
    }

    public function setStartedAt(float $startedAt): Job
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getTerminatedAt(): float
    {
        return $this->terminatedAt;
    }

    public function setTerminatedAt(float $terminatedAt): Job
    {
        $this->terminatedAt = $terminatedAt;

        return $this;
    }

}
