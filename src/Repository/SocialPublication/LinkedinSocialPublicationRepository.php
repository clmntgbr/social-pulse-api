<?php

namespace App\Repository\SocialPublication;

use App\Entity\SocialPublication\LinkedinSocialPublication;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class LinkedinSocialPublicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkedinSocialPublication::class);
    }
}
