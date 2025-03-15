<?php

namespace Bnza\JobManagerBundle\Exception;

use Symfony\Component\Uid\Uuid;
use Throwable;

interface JobExceptionInterface extends ExceptionValuesInterface
{
    public function getJobId(): Uuid;
}
