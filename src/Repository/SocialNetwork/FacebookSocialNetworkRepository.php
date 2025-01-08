<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\FacebookSocialNetwork;
use App\Repository\AbstractRepository;

class FacebookSocialNetworkRepository extends AbstractRepository
{
    public function __construct()
    {
    }

    public function updateOrCreate(array $searchPayload, array $updatePayload): FacebookSocialNetwork
    {
        $account = $this->findOneByCriteria($searchPayload);
        if (!$account) {
            $account = new FacebookSocialNetwork();
        }

        $this->update($account, $updatePayload);

        return $account;
    }
}
