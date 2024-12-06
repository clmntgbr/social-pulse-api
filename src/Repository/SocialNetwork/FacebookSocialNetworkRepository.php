<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\FacebookSocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class FacebookSocialNetworkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacebookSocialNetwork::class);
    }
}
