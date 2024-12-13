<?php

namespace App\Repository\Publication;

use App\Entity\Publication\YoutubePublication;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class YoutubePublicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, YoutubePublication::class);
    }
}
