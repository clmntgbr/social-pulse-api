<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\InstagramSocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class InstagramSocialNetworkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstagramSocialNetwork::class);
    }
}
