<?php

namespace Bnza\JobManagerBundle\Entity;


use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class CachedWorkUnit
{

    #[Groups("Bnza:WorkUnit:read")]
    private Uuid $id;

    #[Groups("Bnza:WorkUnit:read")]
    private Status $status;

    #[Groups("Bnza:WorkUnit:read")]
    private ?int $currentStepNumber = null;

    public function __construct(?array $data = [])
    {
        if (is_array($data)) {
            if (array_key_exists("id", $data)) {
                $this->setId($data['id']);
            }
            if (array_key_exists("status", $data)) {
                $this->setStatus($data['status']);
            }
            if (array_key_exists("current_step_number", $data)) {
                $this->setCurrentStepNumber($data['current_step_number']);
            }
        }
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid|string $id): CachedWorkUnit
    {
        if (is_string($id)) {
            $id = Uuid::fromString($id);
        }

        $this->id = $id;

        return $this;
    }

    public function getCurrentStepNumber(): ?int
    {
        return $this->currentStepNumber;
    }

    public function setCurrentStepNumber(?int $currentStepNumber): CachedWorkUnit
    {
        $this->currentStepNumber = $currentStepNumber;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status|int $status): CachedWorkUnit
    {
        if (is_int($status)) {
            $status = new Status($status);
        }
        $this->status = $status;

        return $this;
    }

}
