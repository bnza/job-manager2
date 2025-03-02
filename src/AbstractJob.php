<?php

namespace Bnza\JobManagerBundle;


use InvalidArgumentException;
use SplQueue;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractJob extends AbstractWorkUnit implements JobInterface
{


    /** @var SplQueue<WorkUnitInterface> */
    protected readonly SplQueue $workUnits;

    /** @var array<WorkUnitInterface> */
    protected array $completedWorkUnits;

    public function __construct(EventDispatcherInterface $eventDispatcher, array $workUnits)
    {
        parent::__construct($eventDispatcher);
        $this->workUnits = new SplQueue();
        foreach ($workUnits as $workUnit) {
            if (!$workUnit instanceof WorkUnitInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'WorkUnit must implement %s: %s given',
                        WorkUnitInterface::class,
                        get_class($workUnit)
                    )
                );
            }
            $this->workUnits->enqueue($workUnit);
        }
        $this->completedWorkUnits = [];
    }

    public final function run(): array
    {
        $this->setUp();
        foreach ($this->workUnits as /** @var WorkUnitInterface $workUnit */ $workUnit) {
            $entity = $workUnit->toEntity()->setParameters($this->parameters);
            $workUnit->configure($entity, $this);
            $workUnitResults = $workUnit->run();
            $this->parameters = array_merge($this->parameters, $workUnitResults);
            $this->completedWorkUnits[] = $workUnit;
        }
        $this->tearDown();

        return $this->returnParameters();
    }

    public function rollback(): void
    {
        for ($i = count($this->completedWorkUnits) - 1; $i >= 0; $i--) {
            $this->workUnits[$i]->rollback();
        }
    }

    public function getStepsCount(): int
    {
        return $this->workUnits->count();
    }

    public final function getType(): string
    {
        return self::WORK_UNIT_TYPE_JOB;
    }
}
