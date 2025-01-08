<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\TwitterSocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class TwitterSocialNetworkRepository extends AbstractRepository
{
    public function __construct()
    {
    }

    public function updateOrCreate(array $searchPayload, array $updatePayload): TwitterSocialNetwork
    {
        $account = $this->findOneByCriteria($searchPayload);
        if (!$account) {
            $account = new TwitterSocialNetwork();
        }

        $this->update($account, $updatePayload);

        return $account;
    }
}
