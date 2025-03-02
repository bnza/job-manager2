<?php

namespace Bnza\JobManagerBundle;

abstract class AbstractTask extends AbstractWorkUnit
{
    public abstract function getSteps(): iterable;

    public abstract function executeStep(mixed $args);

    public final function run(): ?array
    {
        $this->setUp();
        foreach ($this->getSteps() as $step) {
            $this->executeStep($step);
        }
        $this->tearDown();

        return $this->returnParameters();
    }

    public final function getType(): string
    {
        return self::WORK_UNIT_TYPE_TASK;
    }
}
