<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\InstagramSocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class InstagramSocialNetworkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, InstagramSocialNetwork::class);
    }
}
