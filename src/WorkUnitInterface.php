<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Symfony\Component\Uid\Uuid;

interface WorkUnitInterface extends ConfigurableInterface
{
    // Info

    public function getId(): ?Uuid;

    public function getName(): string;

    public function getDescription(): string;

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
