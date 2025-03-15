<?php

namespace Bnza\JobManagerBundle;


use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Bnza\JobManagerBundle\Event\WorkUnitEvent;
use Exception;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractJob extends AbstractWorkUnit implements JobInterface
{


    /** @var array<string, WorkUnitInterface> */
    protected readonly array $workUnits;

    /** @var array<WorkUnitInterface> */
    protected array $completedWorkUnits;

    public function __construct(EventDispatcherInterface $eventDispatcher, array $workUnits)
    {
        parent::__construct($eventDispatcher);
        $_workUnits = [];
        foreach ($workUnits as $id => $workUnit) {
            if (!$workUnit instanceof WorkUnitInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'WorkUnit must implement %s: %s given',
                        WorkUnitInterface::class,
                        get_class($workUnit)
                    )
                );
            }
            $_workUnits[$id] = ($workUnit);
        }
        $this->workUnits = $_workUnits;
        $this->completedWorkUnits = [];
    }

    public final function run(): array
    {
        $event = new WorkUnitEvent($this);
        $this->eventDispatcher->dispatch($event, WorkUnitEvent::SETUP);
        $this->setUp();
        $this->state->getStatus()->running();
        $this->state->setStepsCount($this->getStepsCount());
        $this->state->setStartedAt(microtime(true));
        $this->eventDispatcher->dispatch($event, WorkUnitEvent::STARTED);
        try {
            foreach ($this->workUnits as $serviceId => /** @var WorkUnitInterface $workUnit */ $workUnit) {
                ++$this->currentStepNumber;
                $entity = new WorkUnitEntity()
                    ->setParameters($this->state->getParameters())
                    ->setService($serviceId)
                    ->setParent(
                        $this->getEntity()
                    );
                $workUnit->configure($entity);
                $this->eventDispatcher->dispatch($event, WorkUnitEvent::STEP_STARTED);
                $workUnitResults = $workUnit->run();
                $this->state->setParameters(array_merge($this->state->getParameters(), $workUnitResults));
//                $this->eventDispatcher->dispatch($event, WorkUnitEvent::STEP_TERMINATED);
                $this->completedWorkUnits[] = $workUnit;
            }
            $this->tearDown();
            $this->state->getStatus()->success();
            $this->eventDispatcher->dispatch($event, WorkUnitEvent::SUCCESS);
        } catch (Exception $e) {
            $this->state->getStatus()->error();
            $this->eventDispatcher->dispatch($event, WorkUnitEvent::ERROR);
            $this->rollback();
            $this->eventDispatcher->dispatch($event, WorkUnitEvent::ROLLBACK);
            throw $e;
        } finally {
            $this->state->setTerminatedAt(microtime(true));
            $this->eventDispatcher->dispatch($event, WorkUnitEvent::TERMINATED);
        }

        return $this->returnParameters();
    }

    public function rollback(): void
    {
        for ($i = count($this->completedWorkUnits) - 1; $i >= 0; $i--) {
            $this->completedWorkUnits[$i]->rollback();
        }
    }

    public function getStepsCount(): int
    {
        return count($this->workUnits);
    }

    public final function getType(): string
    {
        return self::WORK_UNIT_TYPE_JOB;
    }
}
