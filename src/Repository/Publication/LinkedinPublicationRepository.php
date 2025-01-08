<?php

namespace App\Repository\Publication;

use App\Entity\Publication\LinkedinPublication;
use App\Repository\AbstractRepository;

class LinkedinPublicationRepository extends AbstractRepository
{
    public function __construct()
    {
    }

    public function create(array $data): LinkedinPublication
    {
        $entity = new LinkedinPublication();

        /** @var LinkedinPublication $entity */
        $entity = $this->update($entity, $data);

        return $entity;
    }
}
