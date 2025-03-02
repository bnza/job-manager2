<?php

namespace Bnza\JobManagerBundle;

class JobServicesIdLocator
{
    /**
     * @var array<string, JobInterface>
     */
    private array $services;

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    public function get(string $serviceId): ?JobInterface
    {
        return $this->services[$serviceId] ?? null;
    }

    public function has(string $serviceId): bool
    {
        return isset($this->services[$serviceId]);
    }

}
