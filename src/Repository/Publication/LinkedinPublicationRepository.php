<?php

namespace App\Repository\Publication;

use App\Entity\Publication\LinkedinPublication;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class LinkedinPublicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, LinkedinPublication::class);
    }

    public function create(array $data): LinkedinPublication
    {
        $entity = new LinkedinPublication();

        /** @var LinkedinPublication $entity */
        $entity = $this->update($entity, $data);

        return $entity;
    }
}
