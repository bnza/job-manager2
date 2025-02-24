<?php

namespace Bnza\JobManagerBundle\Event;

use Bnza\JobManagerBundle\Entity\Job as JobEntity;
use Bnza\JobManagerBundle\JobInterface;
use Symfony\Contracts\EventDispatcher\Event;

class JobEvent extends Event
{
    public const CREATED = 'bnza.job_manager.job.created';
    public const PARAMETERS_SET = 'bnza.job_manager.job.parameters_set';

    public function __construct(private JobInterface $job)
    {
    }

    public function getJob(): JobInterface
    {
        return $this->job;
    }

}
