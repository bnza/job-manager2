<?php

namespace Bnza\JobManagerBundle\Repository;

use Bnza\JobManagerBundle\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class JobRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $metadata = $entityManager->getClassMetadata(Job::class);
        parent::__construct($entityManager, $metadata);
    }
}
