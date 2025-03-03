<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity as JobEntity;

interface JobFactoryInterface
{
    public function createFromEntity(JobEntity $entity): JobInterface;

}
