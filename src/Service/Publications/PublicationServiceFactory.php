<?php

namespace App\Service\Publications;

use App\Enum\SocialNetworkType;
use App\Repository\Publication\LinkedinPublicationRepository;
use App\Repository\SocialNetwork\FacebookSocialNetworkRepository;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use App\Repository\SocialNetwork\TwitterSocialNetworkRepository;
use App\Repository\SocialNetwork\TypeRepository;
use App\Repository\UserRepository;
use App\Service\FacebookApi;
use App\Service\ImageService;
use App\Service\LinkedinApi;
use App\Service\TwitterApi;
use App\Service\ValidatorError;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class PublicationServiceFactory
{
    public function __construct(
        private readonly LinkedinPublicationRepository $linkedinPublicationRepository,
        private readonly LinkedinSocialNetworkRepository $linkedinSocialNetworkRepository,
        private readonly ImageService $imageService
    ){}

    public function getService(string $type): PublicationServiceInterface
    {
        return match ($type) {
            SocialNetworkType::LINKEDIN->toString() => new LinkedinPublicationService(
                $this->linkedinPublicationRepository,
                $this->linkedinSocialNetworkRepository,
                $this->imageService
            ),
            SocialNetworkType::FACEBOOK->toString() => new FacebookPublicationService(),
            SocialNetworkType::TWITTER->toString() => new TwitterPublicationService(),
            default => new DefaultPublicationService(),
        };
    }
}