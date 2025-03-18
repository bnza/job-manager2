<?php

namespace Bnza\JobManagerBundle\Message;

use PHPUnit\Util\PHP\Job;
use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[AsMessage(['async'])]
class JobRunnerMessage
{
    private readonly string $userId;

    public function __construct(private readonly Uuid $id, UserInterface $user)
    {
        $this->userId = $user->getUserIdentifier();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
