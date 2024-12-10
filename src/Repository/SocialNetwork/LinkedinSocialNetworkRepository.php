<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\LinkedinSocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class LinkedinSocialNetworkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkedinSocialNetwork::class);
    }

    public function updateOrCreate(array $searchPayload, array $updatePayload): LinkedinSocialNetwork
    {
        $account = $this->findOneByCriteria($searchPayload);
        if (!$account) {
            $account = new LinkedinSocialNetwork();
        }

        $this->update($account, $updatePayload);
        return $account;
    }
}
