<?php

namespace Bnza\JobManagerBundle\Exception;

use Bnza\JobManagerBundle\Entity\Job;
use RuntimeException;
use Throwable;

class JobCancelledException extends RuntimeException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Job cancelled by user action", $code, $previous);
    }

}
