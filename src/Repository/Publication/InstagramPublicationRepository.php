<?php

namespace App\Repository\Publication;

use App\Entity\Publication\InstagramPublication;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class InstagramPublicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, InstagramPublication::class);
    }
}
