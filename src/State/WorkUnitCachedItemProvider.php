<?php

namespace Bnza\JobManagerBundle\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Bnza\JobManagerBundle\CacheHelper;
use Bnza\JobManagerBundle\Entity\CachedWorkUnit;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

readonly class WorkUnitCachedItemProvider implements ProviderInterface
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

        $cached = $this->cache->get($id);

        if ($cached) {
            if (is_array($cached)) {
                $cached['id'] = $id;
                $a = new CachedWorkUnit($cached);

                return $a;
            }
        }

        $workUnit = $this->entityManager->find(WorkUnitEntity::class, $id);

        if (!is_null($workUnit)) {
            return new CachedWorkUnit()
                ->setId($workUnit->getId())
                ->setStatus($workUnit->getStatus())
                ->setCurrentStepNumber($workUnit->getCurrentStepNumber());
        }

        return null;
    }
}
