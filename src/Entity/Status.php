<?php
declare(strict_types=1);

namespace Bnza\JobManagerBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class Status
{
    const int IDLE = 0b0000;
    const int RUNNING = 0b0001;
    const int SUCCESS = 0b0010;
    const int CANCELLED = 0b0100;
    const int ERROR = 0b1000;

    const string IDLE_TEXT = 'idle';
    const string SUCCESS_TEXT = 'success';
    const string ERROR_TEXT = 'error';
    const string RUNNING_TEXT = 'running';
    const string CANCELLED_TEXT = 'cancelled';

    #[Groups("public")]
    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): Status
    {
        $this->value = $value;

        return $this;
    }

    public function __construct(private int $value = self::IDLE)
    {

    }

    public function __invoke(): int
    {
        return $this->value;
    }

    public function running(): void
    {
        $this->value |= self::RUNNING;
    }

    public function cancel(): void
    {
        $this->value = ~$this->value & self::RUNNING | self::CANCELLED | self::ERROR;
    }

    public function success(): void
    {
        $this->value = $this->value & ~self::RUNNING | self::SUCCESS;
    }

    public function error(): void
    {
        $this->value = $this->value & ~self::RUNNING | self::ERROR;
    }

    #[Groups("public")]
    public function isIdle(): bool
    {
        return self::is($this->value, self::IDLE);
    }

    #[Groups("public")]
    public function isRunning(): bool
    {
        return self::is($this->value, self::RUNNING);
    }

    #[Groups("public")]
    public function isSuccess(): bool
    {
        return self::is($this->value, self::SUCCESS);
    }

    #[Groups("public")]
    public function isCancelled(): bool
    {
        return self::is($this->value, self::CANCELLED);
    }

    #[Groups("public")]
    public function isError(): bool
    {
        return self::is($this->value, self::ERROR);
    }

    public static function is(int $statusValue, int $bitmask): bool
    {
        return match ($bitmask) {
            self::IDLE => $statusValue === self::IDLE,
            self::RUNNING => ($statusValue & self::RUNNING) === self::RUNNING,
            self::SUCCESS => ($statusValue & self::SUCCESS) === self::SUCCESS,
            self::ERROR => ($statusValue & self::ERROR) === self::ERROR,
            self::CANCELLED => ($statusValue & self::CANCELLED) === self::CANCELLED,
            default => false,
        };
    }

    public static function toString(int $statusValue): string
    {
        return match (true) {
            self::is($statusValue, self::IDLE) => self::IDLE_TEXT,
            self::is($statusValue, self::RUNNING) => self::RUNNING_TEXT,
            self::is($statusValue, self::ERROR) => self::ERROR_TEXT,
            self::is($statusValue, self::SUCCESS) => self::SUCCESS_TEXT,
            self::is($statusValue, self::CANCELLED) => self::CANCELLED_TEXT,
            default => 'unknown',
        };
    }
}
