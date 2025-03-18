<?php

namespace Bnza\JobManagerBundle\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Bnza\JobManagerBundle\CacheHelper;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class WorkUnitItemProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CacheHelper $cache,
        private readonly DenormalizerInterface $denormalizer,
        private readonly Security $security,
    ) {

    }

    /**
     * @throws ExceptionInterface
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!array_key_exists("id", $uriVariables)) {
            throw new InvalidArgumentException("Missing 'id' parameter");
        }
        $id = $uriVariables["id"];

        $workUnit = $this->entityManager->find(WorkUnitEntity::class, $id);

        if (!is_null($workUnit) && !$this->security->isGranted('ROLE_ADMIN')) {
            $userId = $this->security->getToken()->getUser()->getUserIdentifier();
            $workUnit = $workUnit->getUserId() === $userId ? $workUnit : null;
        }

        return $workUnit;
    }
}
