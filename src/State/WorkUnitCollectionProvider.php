<?php

namespace Bnza\JobManagerBundle\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class WorkUnitCollectionProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $connection = $this->entityManager->getConnection();

        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedHttpException('Access denied');
        }
        $hasRoleAdmin = $this->security->isGranted('ROLE_ADMIN');

        $sql = <<<'SQL'
WITH RECURSIVE hierarchy AS (
                             SELECT *
                             FROM bnza_job_manager.job
                             WHERE parent_id IS NULL
                             %s

                             UNION ALL

                             SELECT j.*
                             FROM bnza_job_manager.job j
                             JOIN hierarchy h ON j.parent_id = h.id
                         )
                         SELECT *
                         FROM hierarchy
SQL;
        $sql = sprintf($sql, $hasRoleAdmin ? '' : 'AND user_id = :userId');

        $statement = $connection->executeQuery(
            $sql,
            $hasRoleAdmin ? [] : ['userId' => $this->security->getUser()->getUserIdentifier()]
        );

        $resultIds = $statement->fetchFirstColumn();

        if (empty($resultIds)) {
            return [];
        }

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('e')
            ->from(WorkUnitEntity::class, 'e')
            ->where($qb->expr()->in('e.id', ':resultIds'))
            ->setParameter('resultIds', $resultIds);

        return $qb->getQuery()->getResult();
    }
}
