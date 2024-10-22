<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function create(array $data): Post
    {
        $account = new Post();
        return $this->update($account, $data);
    }

    public function update(Post $entity, array $data): Post
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($entity, $method)) {
                $entity->$method($value);
            }
        }

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    public function findByGroupUuid(string $groupUuid)
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $queryBuilder
            ->andWhere("p.groupUuid = :groupUuid")
            ->setParameter('groupUuid', $groupUuid)
            ->orderBy('p.id', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }
}
