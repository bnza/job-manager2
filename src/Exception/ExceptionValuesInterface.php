<?php

namespace Bnza\JobManagerBundle\Exception;

use Throwable;

interface ExceptionValuesInterface extends Throwable
{
    public function getValues(): array;
}
