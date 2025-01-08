<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\LinkedinSocialNetwork;
use App\Repository\AbstractRepository;

class LinkedinSocialNetworkRepository extends AbstractRepository
{
    public function __construct()
    {
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
