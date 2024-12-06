<?php

namespace App\Repository\SocialPublication;

use App\Entity\SocialPublication\InstagramSocialPublication;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class InstagramSocialPublicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstagramSocialPublication::class);
    }
}
