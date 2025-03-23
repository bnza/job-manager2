<?php

namespace Bnza\JobManagerBundle;

use Override;

class WorkUnitDefinition implements WorkUnitDefinitionInterface
{

    public function __construct(
        private readonly string $description,
        private readonly string $service,
        private readonly string $class,
    ) {
    }

    #[Override] public function getDescription(): string
    {
        return $this->description;
    }

    #[Override] public function getService(): string
    {
        return $this->service;
    }

    #[Override] public function getClass(): string
    {
        return $this->class;
    }
}
