<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\Job as JobEntity;
use Symfony\Component\Uid\Uuid;

interface JobInterface
{
    public function getId(): Uuid;

    public function getName(): string;

    public function getDescription(): string;

    public function toEntity(): JobEntity;

    public function hasParameter(string $key): bool;

    public function getParameter(string $key): mixed;

    public function setParameter(string $key, mixed $value, bool $dispatch = true): static;

    public function getParameters(): array;

    public function setParameters(array $parameters, bool $dispatch = true): static;

    public function run(): void;

}
