<?php

namespace Bnza\JobManagerBundle;

class Status
{
    const int IDLE = 0b0000;
    const int RUNNING = 0b0001;
    const int SUCCESS = 0b0010;
    const int CANCELLED = 0b0100;
    const int ERROR = 0b1000;

    public function __construct(private int $value = self::IDLE)
    {

    }

    public function __invoke(): int
    {
        return $this->value;
    }

    public function cancel(): void
    {
        $this->value &= ~self::RUNNING | self::CANCELLED | self::ERROR;
    }

    public function success(): void
    {
        $this->value &= ~self::RUNNING | self::SUCCESS;
    }

    public function error(): void
    {
        $this->value &= ~self::RUNNING | self::ERROR;
    }

    public function isIdle(): bool
    {
        return $this->value = self::IDLE;
    }

    public function isRunning(): bool
    {
        return (bool)($this->value & self::RUNNING);
    }

    public function isSuccess(): bool
    {
        return (bool)($this->value & self::SUCCESS);
    }

    public function isCancelled(): bool
    {
        return (bool)($this->value & self::CANCELLED);
    }

    public function isError(): bool
    {
        return (bool)($this->value & self::ERROR);
    }
}
