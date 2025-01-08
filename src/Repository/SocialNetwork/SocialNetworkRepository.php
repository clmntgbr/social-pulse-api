<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\SocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<SocialNetwork>
 */
class SocialNetworkRepository extends AbstractRepository
{
    public function __construct()
    {
    }
}
