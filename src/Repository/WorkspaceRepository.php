<?php

namespace App\Repository;

use App\Entity\Workspace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Workspace>
 */
class WorkspaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workspace::class);
    }

    public function create(array $data): Workspace
    {
        $workspace = new Workspace();
        $workspace
            ->setLabel($data['label'] ?? null)
            ->setLogoUrl($data['logoUrl'] ?? null);

        $this->getEntityManager()->persist($workspace);
        $this->getEntityManager()->flush();

        return $workspace;
    }
}
