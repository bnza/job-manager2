<?php

namespace Bnza\JobManagerBundle\Entity;

use Bnza\JobManagerBundle\Exception\ReadOnlyPropertyException;
use Exception;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

class WorkUnitEntity
{

    #[Groups("Bnza:WorkUnit:read")]
    private ?Uuid $id = null;
    #[Groups("Bnza:WorkUnit:read")]
    private string $name;
    #[Groups("Bnza:WorkUnit:read")]
    private string $class;
    #[Groups("Bnza:WorkUnit:read")]
    private ?string $service = null;
    #[Groups("Bnza:WorkUnit:read")]
    private string $description;

    private ?string $userId;

    private array $parameters;
    #[Groups("Bnza:WorkUnit:read")]
    private int $stepsCount = 0;
    #[Groups("Bnza:WorkUnit:read")]
    private Status $status;
    #[Groups("Bnza:WorkUnit:read")]
    private ?float $startedAt;
    #[Groups("Bnza:WorkUnit:read")]
    private ?float $terminatedAt;
    private ?WorkUnitEntity $parent;
    #[Groups("Bnza:WorkUnit:read")]
    private Collection $children;
    #[Groups("Bnza:WorkUnit:read")]
    private Collection $errors;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): Uuid|null
    {
        return $this->id;
    }

    public function setId(Uuid $id): WorkUnitEntity
    {
        if (!is_null($this->id)) {
            throw new ReadOnlyPropertyException('id');
        }
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): WorkUnitEntity
    {
        if (isset($this->name)) {
            throw new ReadOnlyPropertyException('name');
        }
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): WorkUnitEntity
    {
        if (isset($this->description)) {
            throw new ReadOnlyPropertyException('description');
        }
        $this->description = $description;

        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): WorkUnitEntity
    {
        if (isset($this->class)) {
            throw new ReadOnlyPropertyException('class');
        }

        $this->class = $class;

        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): WorkUnitEntity
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getStepsCount(): int
    {
        return $this->stepsCount;
    }

    public function setStepsCount(int $stepsCount): WorkUnitEntity
    {
        if ($this->stepsCount > 0) {
            throw new ReadOnlyPropertyException('stepsCount');
        }

        $this->stepsCount = $stepsCount;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): WorkUnitEntity
    {
        if (isset($this->status)) {
            throw new ReadOnlyPropertyException('status');
        }

        $this->status = $status;

        return $this;
    }

    public function getStartedAt(): ?float
    {
        return $this->startedAt;
    }

    public function setStartedAt(float $startedAt): WorkUnitEntity
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getTerminatedAt(): ?float
    {
        return $this->terminatedAt;
    }

    public function setTerminatedAt(float $terminatedAt): WorkUnitEntity
    {
        $this->terminatedAt = $terminatedAt;

        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): WorkUnitEntity
    {
        if (!is_null($this->service)) {
            throw new ReadOnlyPropertyException('service');
        }
        $this->service = $service;

        return $this;
    }

    public function getParent(): ?WorkUnitEntity
    {
        return $this->parent;
    }

    public function setParent(?WorkUnitEntity $parent): WorkUnitEntity
    {
        if (isset($this->parent)) {
            throw new ReadOnlyPropertyException('parent');
        }

        $this->parent = $parent;

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(WorkUnitEntity $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(WorkUnitEntity $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getErrors(): Collection
    {
        return $this->errors;
    }

    public function addError(WorkUnitErrorEntity $error): self
    {
        if (!$this->errors->contains($error)) {
            $this->errors[] = $error;
            $error->setWorkUnit($this);
        }

        return $this;
    }

    public function removeError(WorkUnitErrorEntity $error): self
    {
        if ($this->errors->removeElement($error)) {
            // set the owning side to null (unless already changed)
            if ($error->getWorkUnit() === $this) {
                $error->setWorkUnit(null);
            }
        }

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): WorkUnitEntity
    {
        $this->userId = $userId;

        return $this;
    }

    public function getRoot(): static
    {
        if (!is_null($this->parent)) {
            return $this->parent->getRoot();
        }

        return $this;
    }


}
