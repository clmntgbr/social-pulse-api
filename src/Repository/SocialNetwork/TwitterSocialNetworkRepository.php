<?php

namespace App\Repository\SocialNetwork;

use App\Entity\SocialNetwork\TwitterSocialNetwork;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class TwitterSocialNetworkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, TwitterSocialNetwork::class);
    }

    public function updateOrCreate(array $searchPayload, array $updatePayload): TwitterSocialNetwork
    {
        $account = $this->findOneByCriteria($searchPayload);
        if ($account === null) {
            $account = new TwitterSocialNetwork();
        }

        $this->update($account, $updatePayload);

        return $account;
    }
}
