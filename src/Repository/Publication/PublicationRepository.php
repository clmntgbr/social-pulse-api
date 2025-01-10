<?php

namespace App\Repository\Publication;

use App\Entity\Publication\Publication;
use App\Entity\User;
use App\Enum\PublicationStatus;
use App\Enum\PublicationThreadType;
use App\Repository\AbstractRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class PublicationRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        private Security $security,
    ) {
        parent::__construct($managerRegistry, Publication::class);
    }

    public function findAll(): array
    {
        $qb = $this->createQueryBuilder('p');

        $qb = $this->filters($qb);
        $qb = $this->filterThreadType($qb);
        $qb = $this->filterStatus($qb);

        return $qb->getQuery()->getResult();
    }

    public function findPublicationByThreadUuid(string $uuid): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb = $this->filters($qb);
        $qb = $this->filterStatus($qb);

        $qb
            ->andWhere('p.threadUuid = :uuid')
            ->andWhere('p.threadType IN (:threadType)')
            ->setParameter('threadType', [PublicationThreadType::PRIMARY, PublicationThreadType::SECONDARY])
            ->setParameter('uuid', $uuid)
            ->orderBy('p.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    private function filters(QueryBuilder $queryBuilder): QueryBuilder
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $queryBuilder
            ->join('p.socialNetwork', 's')
            ->andWhere('s.organization = :organization')
            ->setParameter('organization', $user->getActiveOrganization());

        return $queryBuilder;
    }

    private function filterThreadType(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder
            ->andWhere('p.threadType = :threadType')
            ->setParameter('threadType', PublicationThreadType::PRIMARY);

        return $queryBuilder;
    }

    private function filterStatus(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder
            ->andWhere('p.status IN (:status)')
            ->setParameter('status', [
                PublicationStatus::SCHEDULED->toString(),
                PublicationStatus::RETRY->toString(),
                PublicationStatus::POSTED->toString(),
                PublicationStatus::FAILED->toString(),
                PublicationStatus::DRAFT->toString(),
            ]);

        return $queryBuilder;
    }
}