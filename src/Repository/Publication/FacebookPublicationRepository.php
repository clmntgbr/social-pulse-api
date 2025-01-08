<?php

namespace App\Repository\Publication;

use App\Entity\Publication\FacebookPublication;
use App\Repository\AbstractRepository;

class FacebookPublicationRepository extends AbstractRepository
{
    public function __construct()
    {
    }

    public function create(array $data): FacebookPublication
    {
        $entity = new FacebookPublication();

        /** @var FacebookPublication $entity */
        $entity = $this->update($entity, $data);

        return $entity;
    }
}
