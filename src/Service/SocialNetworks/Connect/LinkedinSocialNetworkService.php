<?php

namespace App\Service\SocialNetworks\Connect;

use App\Dto\Api\GetSocialNetworksCallback;
use App\Entity\User;

class LinkedinSocialNetworkService implements SocialNetworkServiceInterface
{
    public function getConnectUrl(User $user, string $callbackPath): string
    {
        return 'https://graph.facebook.com/oauth/access_token';
    }

    public function create(GetSocialNetworksCallback $getSocialNetworksCallback): void
    {

    }
}