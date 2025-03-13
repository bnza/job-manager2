<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity;

interface ConfigurableInterface
{
    public function configure(WorkUnitEntity $entity): void;
}
