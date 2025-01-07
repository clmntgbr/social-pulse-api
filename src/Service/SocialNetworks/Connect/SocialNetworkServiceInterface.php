<?php

namespace App\Service\SocialNetworks\Connect;

use App\Dto\Api\GetSocialNetworksCallback;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

interface SocialNetworkServiceInterface
{
    public function getConnectUrl(User $user, string $callbackPath): ?string;
    public function create(GetSocialNetworksCallback $getSocialNetworksCallback): RedirectResponse;
}