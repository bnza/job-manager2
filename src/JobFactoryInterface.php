<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\Job as JobEntity;

interface JobFactoryInterface
{
    public function createFromEntity(JobEntity $entity): JobInterface;

}
