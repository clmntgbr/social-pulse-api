<?php

namespace App\Service\SocialNetworks\Connect;

use App\Enum\SocialNetworkType;
use App\Repository\UserRepository;
use App\Service\FacebookApi;

class SocialNetworkServiceFactory
{
    public function __construct(
        private readonly FacebookApi $facebookApi,
        private readonly UserRepository $userRepository,
        private readonly string $facebookLoginUrl,
        private readonly string $facebookClientId,
        private readonly string $facebookCallbackUrl
    ) {}

    public function getService(string $type): SocialNetworkServiceInterface
    {
        return match ($type) {
            SocialNetworkType::LINKEDIN->toString() => new LinkedinSocialNetworkService(),
            SocialNetworkType::FACEBOOK->toString() => new FacebookSocialNetworkService(
                $this->facebookApi,
                $this->userRepository,
                $this->facebookLoginUrl,
                $this->facebookClientId,
                $this->facebookCallbackUrl
            ),
            SocialNetworkType::TWITTER->toString() => new TwitterSocialNetworkService(),
        };
    }
}