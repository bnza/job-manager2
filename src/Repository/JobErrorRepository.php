<?php

namespace Bnza\JobManagerBundle\Repository;

use Bnza\JobManagerBundle\Entity\WorkUnitErrorEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class JobErrorRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $metadata = $entityManager->getClassMetadata(WorkUnitErrorEntity::class);
        parent::__construct($entityManager, $metadata);
    }
}
