<?php

namespace Bnza\JobManagerBundle\MessageHandler;

use Bnza\JobManagerBundle\Exception\ExceptionValuesInterface;
use Bnza\JobManagerBundle\Exception\JobException;
use Bnza\JobManagerBundle\Exception\JobExceptionInterface;
use Bnza\JobManagerBundle\JobRunner;
use Bnza\JobManagerBundle\Message\JobRunnerMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

class JobRunnerMessageHandler
{
    public function __construct(private readonly JobRunner $runner)
    {

    }

    /**
     * @throws JobExceptionInterface
     */
    #[AsMessageHandler]
    public function __invoke(JobRunnerMessage $message): void
    {
        try {
            $id = $message->getId();
            $this->runner->run($id);
        } catch (JobExceptionInterface $exception) {
            throw $exception;
        } catch (ExceptionValuesInterface $exception) {
            throw new JobException($id, $exception->getValues(), $exception->getCode(), $exception);
        } catch (Throwable $exception) {
            throw new JobException($id, [], $exception->getCode(), $exception);
        }

    }
}
