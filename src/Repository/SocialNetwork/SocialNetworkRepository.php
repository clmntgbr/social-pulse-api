<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\SocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SocialNetwork>
 */
class SocialNetworkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, SocialNetwork::class);
    }
}
