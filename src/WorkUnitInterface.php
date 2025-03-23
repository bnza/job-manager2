<?php

namespace Bnza\JobManagerBundle;

use Symfony\Component\Uid\Uuid;

interface WorkUnitInterface extends ConfigurableInterface, WorkUnitDefinitionInterface
{
    // Info

    public function getId(): ?Uuid;

    public function getType(): string;

    // Workflow

    /**
     * @return array<string, mixed> | null
     */
    public function run(): array|null;

    public function setUp(): void;

    public function tearDown(): void;

    public function rollback(): void;

    public function getStepsCount(): int;

    public function getCurrentStepNumber(): int;

    // Status

    public function getStatusValue(): int;

    public function isIdle(): bool;

    public function isRunning(): bool;

    public function isCancelled(): bool;

    public function isSuccess(): bool;

    public function isError(): bool;

    public function cancel(): void;

}
