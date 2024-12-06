<?php

namespace App\Repository\SocialPublication;

use App\Entity\SocialPublication\FacebookSocialPublication;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class FacebookSocialPublicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacebookSocialPublication::class);
    }
}
