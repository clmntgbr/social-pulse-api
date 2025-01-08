<?php

namespace App\Service\Publications;

use App\Enum\SocialNetworkType;
use App\Repository\Publication\FacebookPublicationRepository;
use App\Repository\Publication\LinkedinPublicationRepository;
use App\Repository\Publication\TwitterPublicationRepository;
use App\Repository\SocialNetwork\FacebookSocialNetworkRepository;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use App\Repository\SocialNetwork\TwitterSocialNetworkRepository;

readonly class PublicationServiceFactory
{
    public function __construct(
        private readonly LinkedinPublicationRepository $linkedinPublicationRepository,
        private readonly LinkedinSocialNetworkRepository $linkedinSocialNetworkRepository,
        private readonly FacebookPublicationRepository $facebookPublicationRepository,
        private readonly FacebookSocialNetworkRepository $facebookSocialNetworkRepository,
        private readonly TwitterPublicationRepository $twitterPublicationRepository,
        private readonly TwitterSocialNetworkRepository $twitterSocialNetworkRepository,
        private readonly PublicationService $publicationService,
    ) {
    }

    public function getService(string $type): PublicationServiceInterface
    {
        return match ($type) {
            SocialNetworkType::LINKEDIN->toString() => new LinkedinPublicationService(
                $this->linkedinPublicationRepository,
                $this->linkedinSocialNetworkRepository,
                $this->publicationService
            ),
            SocialNetworkType::FACEBOOK->toString() => new FacebookPublicationService(
                $this->facebookPublicationRepository,
                $this->facebookSocialNetworkRepository,
                $this->publicationService
            ),
            SocialNetworkType::TWITTER->toString() => new TwitterPublicationService(
                $this->twitterPublicationRepository,
                $this->twitterSocialNetworkRepository,
                $this->publicationService
            ),
            default => new DefaultPublicationService(),
        };
    }
}
