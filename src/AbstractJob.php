<?php

namespace Bnza\JobManagerBundle;


use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Bnza\JobManagerBundle\Event\WorkUnitEvent;
use ErrorException;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

abstract class AbstractJob extends AbstractWorkUnit implements JobInterface
{


    /** @var array<string, WorkUnitInterface> */
    protected readonly array $workUnits;

    /** @var array<WorkUnitInterface> */
    protected array $completedWorkUnits;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        array $workUnits,
        LoggerInterface $logger
    ) {
        parent::__construct($eventDispatcher, $logger);
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
            $previousErrorHandler = set_error_handler(
                [$this, 'handleError'],
                E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_PARSE
            );
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
                $this->completedWorkUnits[] = $workUnit;
            }
            $this->tearDown();
            $this->state->getStatus()->success();
            $this->eventDispatcher->dispatch($event, WorkUnitEvent::SUCCESS);
        } catch (Throwable $e) {
            $this->state->getStatus()->error();
            $this->eventDispatcher->dispatch($event, WorkUnitEvent::ERROR);
            $this->rollback();
            $this->eventDispatcher->dispatch($event, WorkUnitEvent::ROLLBACK);
            throw $e;
        } finally {
            $this->state->setTerminatedAt(microtime(true));
            $this->eventDispatcher->dispatch($event, WorkUnitEvent::TERMINATED);
            set_error_handler($previousErrorHandler);
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

    /**
     * @throws ErrorException
     */
    private function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
