<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\Type;
use App\Repository\AbstractRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Type>
 */
class TypeRepository extends AbstractRepository
{
    public function __construct()
    {
    }
}
