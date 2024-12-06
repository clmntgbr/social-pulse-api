<?php

namespace App\Repository\SocialPublication;

use App\Entity\SocialPublication\YoutubeSocialPublication;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class YoutubeSocialPublicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, YoutubeSocialPublication::class);
    }
}
