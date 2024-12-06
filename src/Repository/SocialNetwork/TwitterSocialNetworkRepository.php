<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\TwitterSocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class TwitterSocialNetworkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwitterSocialNetwork::class);
    }
}
