<?php

namespace App\Repository\Publication;

use App\Entity\Publication\Publication;
use App\Entity\User;
use App\Enum\PublicationStatus;
use App\Enum\PublicationThreadType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Publication::class);
    }

    /**
     * @throws \Exception
     */
    public function findAllWithFilters(User $user): array
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.socialNetwork', 's')
            ->andWhere('s.organization = :organization')
            ->andWhere('p.status IN (:status)')
            ->andWhere('p.threadType = :threadType')
            ->setParameter('threadType', PublicationThreadType::PRIMARY)
            ->setParameter('organization', $user->getActiveOrganization())
            ->setParameter('status', [
                PublicationStatus::PROGRAMMED->toString(),
                PublicationStatus::POSTED->toString(),
                PublicationStatus::FAILED->toString(),
                PublicationStatus::DRAFT->toString()
            ]);

        return $qb->getQuery()->getResult();
    }
}
