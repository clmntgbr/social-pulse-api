<?php

namespace App\Service\Publications;

use App\Enum\SocialNetworkType;
use App\Repository\Publication\FacebookPublicationRepository;
use App\Repository\Publication\LinkedinPublicationRepository;
use App\Repository\Publication\TwitterPublicationRepository;
use App\Repository\SocialNetwork\FacebookSocialNetworkRepository;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use App\Repository\SocialNetwork\TwitterSocialNetworkRepository;
use App\Service\ImageService;
use App\Service\TwitterApi;
use Symfony\Component\Messenger\MessageBusInterface;

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
        private readonly TwitterApi $twitterApi,
        private readonly ImageService $imageService,
        private readonly MessageBusInterface $messageBus,
        private readonly string $projectRoot
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
                $this->publicationService,
                $this->twitterApi,
                $this->imageService,
                $this->messageBus,
                $this->projectRoot
            ),
            default => new DefaultPublicationService(),
        };
    }
}