<?php

namespace Bnza\JobManagerBundle\Exception;

use RuntimeException;
use Throwable;

class ReadOnlyPropertyException extends RuntimeException
{
    public function __construct(string $property, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Property \"$property\" is already set", $code, $previous);
    }
}
