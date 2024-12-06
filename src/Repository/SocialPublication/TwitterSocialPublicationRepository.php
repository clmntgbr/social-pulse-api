<?php

namespace App\Repository\SocialPublication;

use App\Entity\SocialPublication\TwitterSocialPublication;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class TwitterSocialPublicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwitterSocialPublication::class);
    }
}
