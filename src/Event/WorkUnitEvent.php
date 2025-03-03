<?php

namespace Bnza\JobManagerBundle\Event;

use Bnza\JobManagerBundle\WorkUnitInterface;
use Symfony\Contracts\EventDispatcher\Event;

class WorkUnitEvent extends Event
{
    public const PRE_CONFIGURE = 'bnza.job_manager.work_unit.configure.pre';
    public const POST_CONFIGURE = 'bnza.job_manager.work_unit.configure.post';
    public const SETUP = 'bnza.job_manager.work_unit.setup';
    public const PARAMETERS_SET = 'bnza.job_manager.work_unit.parameters_set';
    public const STARTED = 'bnza.job_manager.work_unit.started';
    public const STEP_STARTED = 'bnza.job_manager.work_unit.step_started';
    public const STEP_TERMINATED = 'bnza.job_manager.work_unit.step_terminated';
    public const TERMINATED = 'bnza.job_manager.work_unit.terminated';
    public const SUCCESS = 'bnza.job_manager.work_unit.success';
    public const ERROR = 'bnza.job_manager.work_unit.error';
    public const CANCELLED = 'bnza.job_manager.work_unit.cancelled';
    public const ROLLBACK = 'bnza.job_manager.work_unit.rollback';
    public const TEARDOWN = 'bnza.job_manager.work_unit.teardown';

    public function __construct(private WorkUnitInterface $workUnit)
    {
    }

    public function getWorkUnit(): WorkUnitInterface
    {
        return $this->workUnit;
    }

}
