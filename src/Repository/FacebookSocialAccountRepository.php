<?php

namespace App\Repository;

use App\Entity\FacebookSocialAccount;
use App\Enum\SocialAccountStatus;
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

    public function create(array $data): FacebookSocialAccount
    {
        $account = new FacebookSocialAccount();
        $account
            ->setRefreshUuid($data['refreshUuid'] ?? null)
            ->setSocialAccountId($data['socialAccountId'] ?? null)
            ->setIsVerified($data['isVerified'] ?? false)
            ->setUsername($data['username'] ?? null)
            ->setName($data['name'] ?? null)
            ->setStatus($data['status'] ?? SocialAccountStatus::TEMPORARY->toString())
            ->setToken($data['token'] ?? null)
            ->setBearerToken($data['bearerToken'] ?? null)
            ->setRefreshToken($data['refreshToken'] ?? null)
            ->setTokenSecret($data['tokenSecret'] ?? null)
            ->setScopes($data['scopes'] ?? [])
            ->setEmail($data['email'] ?? null);

        $this->getEntityManager()->persist($account);
        $this->getEntityManager()->flush();

        return $account;
    }

    public function update(FacebookSocialAccount $entity, array $data): object
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

    public function delete(FacebookSocialAccount $entity): void
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
