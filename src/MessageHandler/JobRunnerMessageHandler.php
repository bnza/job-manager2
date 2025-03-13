<?php

namespace Bnza\JobManagerBundle\MessageHandler;

use Bnza\JobManagerBundle\JobRunner;
use Bnza\JobManagerBundle\Message\JobRunnerMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

class JobRunnerMessageHandler
{
    public function __construct(private readonly JobRunner $runner)
    {

    }

    #[AsMessageHandler]
    public function __invoke(JobRunnerMessage $message): void
    {
        $id = $message->getId();
        try {
            $this->runner->run($id);
        } catch (Throwable $e) {
            echo $e;
        }
    }
}
