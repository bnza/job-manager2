<?php

namespace Bnza\JobManagerBundle\Tests;

use Bnza\JobManagerBundle\Entity\Status;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class EntityStatusTest extends TestCase
{
    #[TestWith([0, Status::IDLE, true])]
    #[TestWith([1, Status::IDLE, false])]
    #[TestWith([1, Status::RUNNING, true])]
    #[TestWith([0b10, Status::RUNNING, false])]
    #[TestWith([0b11, Status::RUNNING, true])]
    public function testStatusIs(int $value, int $mask, bool $expected): void
    {
        $this->assertEquals($expected, Status::is($value, $mask));
    }

    public function testSuccessWorkflow()
    {
        $status = new Status();
        $this->assertTrue($status->isIdle());
        $this->assertFalse($status->isRunning());
        $this->assertFalse($status->isSuccess());
        $this->assertFalse($status->isError());
        $this->assertFalse($status->isCancelled());
        $status->running();
        $this->assertFalse($status->isIdle());
        $this->assertTrue($status->isRunning());
        $this->assertFalse($status->isSuccess());
        $this->assertFalse($status->isError());
        $this->assertFalse($status->isCancelled());
        $status->success();
        $this->assertFalse($status->isIdle());
        $this->assertFalse($status->isRunning());
        $this->assertTrue($status->isSuccess());
        $this->assertFalse($status->isError());
        $this->assertFalse($status->isCancelled());
    }

    public function testCancelWorkflow()
    {
        $status = new Status();
        $status->running();
        $status->cancel();
        $this->assertFalse($status->isIdle());
        $this->assertFalse($status->isRunning());
        $this->assertFalse($status->isSuccess());
        $this->assertTrue($status->isError());
        $this->assertTrue($status->isCancelled());
    }

    public function testErrorWorkflow()
    {
        $status = new Status();
        $status->running();
        $status->error();
        $this->assertFalse($status->isIdle());
        $this->assertFalse($status->isRunning());
        $this->assertFalse($status->isSuccess());
        $this->assertTrue($status->isError());
        $this->assertFalse($status->isCancelled());
    }
}
