<?php

namespace App\Repository\Publication;

use App\Entity\Publication\FacebookPublication;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class FacebookPublicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, FacebookPublication::class);
    }

    public function create(array $data): FacebookPublication
    {
        $entity = new FacebookPublication();

        /** @var FacebookPublication $entity */
        $entity = $this->update($entity, $data);

        return $entity;
    }
}
