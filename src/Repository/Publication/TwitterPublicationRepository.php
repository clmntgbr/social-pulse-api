<?php

namespace App\Repository\Publication;

use App\Entity\Publication\TwitterPublication;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class TwitterPublicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwitterPublication::class);
    }
}
