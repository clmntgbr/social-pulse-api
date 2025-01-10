<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\YoutubeSocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class YoutubeSocialNetworkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, YoutubeSocialNetwork::class);
    }
}
