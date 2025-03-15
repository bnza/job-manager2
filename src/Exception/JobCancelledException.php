<?php

namespace Bnza\JobManagerBundle\Exception;

use RuntimeException;
use Symfony\Component\Uid\Uuid;
use Throwable;

class JobCancelledException extends RuntimeException implements JobExceptionInterface
{
    public function __construct(private Uuid $id, int $code = 0, ?Throwable $previous = null)
    {
        $this->message = "Job \"$id\" cancelled by user action.";
        parent::__construct($this->message, $code, $previous);
    }

    public function getJobId(): Uuid
    {
        return $this->id;
    }

    public function getValues(): array
    {
        return [];
    }

}
