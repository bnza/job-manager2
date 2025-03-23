<?php

namespace Bnza\JobManagerBundle;

use Symfony\Component\Uid\Uuid;

interface WorkUnitDefinitionInterface
{

    public function getDescription(): string;

    public function getService(): string;

    public function getClass(): string;
}
