<?php

namespace Bnza\JobManagerBundle;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

abstract class AbstractJobFactory implements JobFactoryInterface
{

    public function __construct(protected readonly EventDispatcherInterface $eventDispatcher)
    {
    }
}
