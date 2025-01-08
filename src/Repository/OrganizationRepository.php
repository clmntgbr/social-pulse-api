<?php

namespace App\Repository;

use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Organization>
 */
class OrganizationRepository extends AbstractRepository
{
    public function __construct()
    {
    }

    public function create(array $data): Organization
    {
        $organization = new Organization();

        /** @var Organization $organization */
        $organization = $this->update($organization, $data);

        return $organization;
    }
}
