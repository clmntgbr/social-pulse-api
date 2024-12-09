<?php

namespace App\Service\SocialNetworks\Connect;

use App\Dto\Api\GetSocialNetworksCallback;
use App\Entity\User;

interface SocialNetworkServiceInterface
{
    public function getConnectUrl(User $user, string $callbackPath): string;
    public function create(GetSocialNetworksCallback $getSocialNetworksCallback): void;
}