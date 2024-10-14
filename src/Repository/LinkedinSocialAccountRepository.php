<?php

namespace App\Repository;

use App\Entity\LinkedinSocialAccount;
use App\Enum\SocialAccountStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LinkedinSocialAccount>
 */
class LinkedinSocialAccountRepository extends ServiceEntityRepository implements InterfaceSocialAccountRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkedinSocialAccount::class);
    }

    public function create(array $data): LinkedinSocialAccount
    {
        $account = new LinkedinSocialAccount();
        $account
            ->setRefreshUuid($data['refreshUuid'] ?? null)
            ->setSocialAccountId($data['socialAccountId'] ?? null)
            ->setIsVerified($data['isVerified'] ?? false)
            ->setUsername($data['username'] ?? null)
            ->setName($data['name'] ?? null)
            ->setStatus($data['status'] ?? SocialAccountStatus::TEMPORARY)
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
}
