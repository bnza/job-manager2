<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity;

interface WorkUnitFactoryInterface
{
    public function supports(string $id): bool;

    public function create(): WorkUnitInterface;

    public function toEntity(): WorkUnitEntity;

}
