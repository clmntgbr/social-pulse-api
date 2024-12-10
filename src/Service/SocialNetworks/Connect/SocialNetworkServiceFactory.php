<?php

namespace App\Service\SocialNetworks\Connect;

use App\Enum\SocialNetworkType;
use App\Repository\SocialNetwork\FacebookSocialNetworkRepository;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use App\Repository\UserRepository;
use App\Service\FacebookApi;
use App\Service\LinkedinApi;

readonly class SocialNetworkServiceFactory
{
    public function __construct(
        private FacebookApi                     $facebookApi,
        private LinkedinApi                     $linkedinApi,
        private UserRepository                  $userRepository,
        private FacebookSocialNetworkRepository $socialNetworkRepository,
        private LinkedinSocialNetworkRepository $linkedinSocialNetworkRepository,
        private string                          $facebookLoginUrl,
        private string                          $facebookClientId,
        private string                          $facebookCallbackUrl,
        private string                          $linkedinLoginUrl,
        private string                          $linkedinClientId,
        private string                          $linkedinCallbackUrl,
        private string                          $frontUrl
    ) {}

    public function getService(string $type): SocialNetworkServiceInterface
    {
        return match ($type) {
            SocialNetworkType::LINKEDIN->toString() => new LinkedinSocialNetworkService(
                $this->linkedinApi,
                $this->userRepository,
                $this->linkedinSocialNetworkRepository,
                $this->linkedinLoginUrl,
                $this->linkedinClientId,
                $this->linkedinCallbackUrl,
                $this->frontUrl
            ),
            SocialNetworkType::FACEBOOK->toString() => new FacebookSocialNetworkService(
                $this->facebookApi,
                $this->userRepository,
                $this->socialNetworkRepository,
                $this->facebookLoginUrl,
                $this->facebookClientId,
                $this->facebookCallbackUrl,
                $this->frontUrl
            ),
            SocialNetworkType::TWITTER->toString() => new TwitterSocialNetworkService(
                $this->frontUrl
            ),
            default => new DefaultSocialNetworkService(
                $this->frontUrl
            ),
        };
    }
}