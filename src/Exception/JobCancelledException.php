<?php

namespace Bnza\JobManagerBundle\Exception;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use RuntimeException;
use Throwable;

class JobCancelledException extends RuntimeException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("WorkUnitEntity cancelled by user action", $code, $previous);
    }

}
