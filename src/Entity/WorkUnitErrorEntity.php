<?php

namespace Bnza\JobManagerBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class WorkUnitErrorEntity
{

    #[Groups("Bnza:WorkUnit:read")]
    private ?Uuid $id = null;

    private ?WorkUnitEntity $workUnit;

    #[Groups("Bnza:WorkUnit:read")]
    private string $class;

    #[Groups("Bnza:WorkUnit:read")]
    private string $message;

    #[Groups("Bnza:WorkUnit:read")]
    private array $values;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getWorkUnit(): ?WorkUnitEntity
    {
        return $this->workUnit;
    }

    public function setWorkUnit(?WorkUnitEntity $workUnit): WorkUnitErrorEntity
    {
        $this->workUnit = $workUnit;

        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): WorkUnitErrorEntity
    {
        $this->class = $class;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): WorkUnitErrorEntity
    {
        $this->message = $message;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): WorkUnitErrorEntity
    {
        $this->values = $values;

        return $this;
    }

    public function getValue(string $string): mixed
    {
        return array_key_exists($string, $this->values) ? $this->values[$string] : null;
    }

    public function setValue(string $string, mixed $value): WorkUnitErrorEntity
    {
        $this->values[$string] = $value;

        return $this;
    }
}
