<?php

namespace App\Repository\Publication;

use App\Entity\Publication\LinkedinPublication;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class LinkedinPublicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkedinPublication::class);
    }

    public function create(array $data): LinkedinPublication
    {
        $entity = new LinkedinPublication();

        /** @var LinkedinPublication $entity */
        $entity = $this->update($entity, $data);

        return $entity;
    }
}
