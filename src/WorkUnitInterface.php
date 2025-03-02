<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\Job as JobEntity;
use Symfony\Component\Uid\Uuid;

interface WorkUnitInterface
{

    public function getId(): ?Uuid;

    public function getName(): string;

    public function getDescription(): string;

    public function getType(): string;

    /**
     * @return array<string, mixed> | null
     */
    public function run(): array|null;

    public function configure(JobEntity $entity, ?JobInterface $parent = null): void;

    public function setUp(): void;

    public function tearDown(): void;

    public function toEntity(): JobEntity;

    public function getStepsCount(): int;

    public function getCurrentStepNumber(): int;

    public function isIdle(): bool;

    public function isRunning(): bool;

    public function isCancelled(): bool;

    public function isSuccess(): bool;

    public function isError(): bool;

    public function cancel(): void;

    public function rollback(): void;

}
