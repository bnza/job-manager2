<?php

namespace Bnza\JobManagerBundle;

interface WorkerInterface extends ConfigurableInterface
{
    public function getSteps(): iterable;

    public function executeStep(int $index, mixed $args): void;

    public function getStepsCount(): int;

    public function configure(Entity\WorkUnitEntity $entity): void;

    public function tearDown(): void;
}
