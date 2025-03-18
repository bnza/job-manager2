<?php

namespace Bnza\JobManagerBundle\MessageHandler;

use Bnza\JobManagerBundle\Exception\ExceptionValuesInterface;
use Bnza\JobManagerBundle\Exception\JobException;
use Bnza\JobManagerBundle\Exception\JobExceptionInterface;
use Bnza\JobManagerBundle\JobRunner;
use Bnza\JobManagerBundle\Message\JobRunnerMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Throwable;

class JobRunnerMessageHandler
{
    private readonly ?TokenInterface $originalToken;

    public function __construct(
        private readonly JobRunner $runner,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly UserProviderInterface $userProvider,
    ) {
        $this->originalToken = $this->tokenStorage->getToken();
    }

    /**
     * @throws JobExceptionInterface
     */
    #[AsMessageHandler]
    public function __invoke(JobRunnerMessage $message): void
    {
        try {
            $user = $this->userProvider->loadUserByIdentifier($message->getUserId());
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);
            $id = $message->getId();
            $this->runner->run($id);
        } catch (JobExceptionInterface $exception) {
            throw $exception;
        } catch (ExceptionValuesInterface $exception) {
            throw new JobException($id, $exception->getValues(), $exception->getCode(), $exception);
        } catch (Throwable $exception) {
            throw new JobException($id, [], $exception->getCode(), $exception);
        } finally {
            $this->tokenStorage->setToken($this->originalToken);
        }

    }
}
