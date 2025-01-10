<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\FacebookSocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class FacebookSocialNetworkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, FacebookSocialNetwork::class);
    }

    public function updateOrCreate(array $searchPayload, array $updatePayload): FacebookSocialNetwork
    {
        $account = $this->findOneByCriteria($searchPayload);
        if ($account === null) {
            $account = new FacebookSocialNetwork();
        }

        $this->update($account, $updatePayload);

        return $account;
    }
}
