<?php

namespace App\Repository\Publication;

use App\Entity\Publication\TwitterPublication;
use App\Repository\AbstractRepository;

class TwitterPublicationRepository extends AbstractRepository
{
    public function __construct()
    {
    }

    public function create(array $data): TwitterPublication
    {
        $entity = new TwitterPublication();

        /** @var TwitterPublication $entity */
        $entity = $this->update($entity, $data);

        return $entity;
    }
}
