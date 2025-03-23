<?php

namespace Bnza\JobManagerBundle;

final readonly class WorkUnitDefinitionServiceLocator
{
    public function __construct(
        /** @var array<string,WorkUnitDefinitionInterface> * $definitions */
        private array $definitions
    ) {
    }

    public function get(string $serviceId): ?WorkUnitDefinitionInterface
    {
        return $this->definitions[$serviceId] ?? null;
    }

    public function has(string $serviceId): bool
    {
        return array_key_exists($serviceId, $this->definitions);
    }
}
