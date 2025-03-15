<?php

namespace Bnza\JobManagerBundle\Exception;

use Exception;
use Symfony\Component\Uid\Uuid;
use Throwable;

class JobException extends Exception implements JobExceptionInterface
{
    public function __construct(
        protected readonly ?Uuid $id,
        protected array $values,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->message = $previous ? $previous->getMessage() : "Job \"$id\" exception.";
        parent::__construct($this->message, $code, $previous);
    }

    public function getJobId(): Uuid
    {
        return $this->id;
    }

    public function getValues(): array
    {
        $previous = $this->getPrevious();
        $hasValues = $previous instanceof ExceptionValuesInterface;
        $values = $hasValues ? $previous->getValues() : [];

        return count($this->values) === 0 ? $values : $this->values;
    }
}
