<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Event\WorkUnitEvent;
use Exception;

abstract class AbstractTask extends AbstractWorkUnit implements WorkerInterface
{
    public abstract function getSteps(): iterable;

    public abstract function executeStep(int $index, mixed $args): void;

    public final function run(): ?array
    {
        $event = new WorkUnitEvent($this);
        $this->eventDispatcher->dispatch($event, WorkUnitEvent::SETUP);
        $this->setUp();
        $this->state->getStatus()->running();
        $this->state->setStepsCount($this->getStepsCount());
        $this->state->setStartedAt(microtime(true));
        $this->eventDispatcher->dispatch($event, WorkUnitEvent::STARTED);
        try {
            foreach ($this->getSteps() as $i => $step) {
                ++$this->currentStepNumber;
                $this->eventDispatcher->dispatch($event, WorkUnitEvent::STEP_STARTED);
                $this->executeStep($i, $step);
                $this->eventDispatcher->dispatch($event, WorkUnitEvent::STEP_TERMINATED);
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

    public final function getType(): string
    {
        return self::WORK_UNIT_TYPE_TASK;
    }
}
