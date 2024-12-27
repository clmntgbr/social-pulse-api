<?php

namespace App\Repository\Publication;

use App\Entity\Publication\Publication;
use App\Entity\User;
use App\Enum\PublicationStatus;
use App\Repository\AbstractRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Enum\PublicationThreadType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PublicationRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private Security $security
    ) {
        parent::__construct($registry, Publication::class);
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

    private function filters(QueryBuilder $builder): QueryBuilder
    {
        $builder
            ->join('p.socialNetwork', 's')
            ->andWhere('s.organization = :organization')
            ->setParameter('organization', $this->security->getUser()->getActiveOrganization());

        return $builder;
    }

    private function filterThreadType(QueryBuilder $builder): QueryBuilder
    {
        $builder
            ->andWhere('p.threadType = :threadType')
            ->setParameter('threadType', PublicationThreadType::PRIMARY);

        return $builder;
    }

    private function filterStatus(QueryBuilder $builder): QueryBuilder
    {
        $builder
            ->andWhere('p.status IN (:status)')
            ->setParameter('status', [
                PublicationStatus::SCHEDULED->toString(),
                PublicationStatus::POSTED->toString(),
                PublicationStatus::FAILED->toString(),
                PublicationStatus::DRAFT->toString()
            ]);

        return $builder;
    }
}
