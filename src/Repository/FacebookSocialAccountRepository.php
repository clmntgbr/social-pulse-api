<?php

namespace App\Repository;

use App\Entity\FacebookSocialAccount;
use App\Entity\SocialAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FacebookSocialAccount>
 */
class FacebookSocialAccountRepository extends ServiceEntityRepository implements InterfaceSocialAccountRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacebookSocialAccount::class);
    }

    public function create(array $data): SocialAccount
    {
        $account = new FacebookSocialAccount();
        return $this->update($account, $data);
    }

    public function update(SocialAccount $entity, array $data): SocialAccount
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

    public function delete(SocialAccount $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function updateOrCreate(array $searchPayload, array $updatePayload): FacebookSocialAccount
    {
        $account = $this->findByCriteria($searchPayload);
        if (!$account) {
            $account = new FacebookSocialAccount();
        }

        $this->update($account, $updatePayload);
        return $account;
    }

    private function findByCriteria(array $criteria): ?FacebookSocialAccount
    {
        $queryBuilder = $this->createQueryBuilder('p');

        foreach ($criteria as $key => $value) {
            $queryBuilder->andWhere("p.$key = :$key")
                ->setParameter($key, $value);
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
