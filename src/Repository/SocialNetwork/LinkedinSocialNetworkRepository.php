<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\LinkedinSocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class LinkedinSocialNetworkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkedinSocialNetwork::class);
    }
}
