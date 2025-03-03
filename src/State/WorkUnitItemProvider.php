<?php

namespace Bnza\JobManagerBundle\State;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Bnza\JobManagerBundle\CacheHelper;
use Bnza\JobManagerBundle\Entity\Status;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Uid\Uuid;

class WorkUnitItemProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CacheHelper $cache,
        private readonly DenormalizerInterface $denormalizer,
    ) {

    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!($operation instanceof Get)) {
            throw new InvalidArgumentException("Operation accepts only Get");
        }
        if (!array_key_exists("id", $uriVariables)) {
            throw new InvalidArgumentException("Missing 'id' parameter");
        }
        $id = $uriVariables["id"];

        $item = $this->cache->get($id);
        if (!is_null($item)) {
            $_item = array_merge([],
                $item,
                ['id' => new Uuid($item['id']), 'status' => new Status($item['status']['value'])]);
            $entity = $this->denormalizer->denormalize($_item, WorkUnitEntity::class, null);

            return $entity;
        }

        return $this->entityManager->find(WorkUnitEntity::class, $id);

    }
}
