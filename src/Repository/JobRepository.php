<?php

namespace Bnza\JobManagerBundle\Repository;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class JobRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $metadata = $entityManager->getClassMetadata(WorkUnitEntity::class);
        parent::__construct($entityManager, $metadata);
    }
}
