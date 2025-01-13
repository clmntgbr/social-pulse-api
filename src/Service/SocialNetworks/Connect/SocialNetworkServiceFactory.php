<?php

namespace App\Service\SocialNetworks\Connect;

use App\Enum\SocialNetworkType;
use App\Repository\SocialNetwork\FacebookSocialNetworkRepository;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use App\Repository\SocialNetwork\TwitterSocialNetworkRepository;
use App\Repository\SocialNetwork\TypeRepository;
use App\Repository\UserRepository;
use App\Service\FacebookApi;
use App\Service\LinkedinApi;
use App\Service\TwitterApi;
use App\Service\ValidatorError;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class SocialNetworkServiceFactory
{
    public function __construct(
        private FacebookApi $facebookApi,
        private LinkedinApi $linkedinApi,
        private TwitterApi $twitterApi,
        private UserRepository $userRepository,
        private TypeRepository $typeRepository,
        private FacebookSocialNetworkRepository $facebookSocialNetworkRepository,
        private LinkedinSocialNetworkRepository $linkedinSocialNetworkRepository,
        private TwitterSocialNetworkRepository $twitterSocialNetworkRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private ValidatorError $validatorError,
        private string $facebookLoginUrl,
        private string $facebookClientId,
        private string $callbackUrl,
        private string $linkedinLoginUrl,
        private string $linkedinClientId,
        private string $twitterApiUrl,
        private string $twitterApiKey,
        private string $twitterApiSecret,
        private string $frontUrl,
    ) {
    }

    public function getService(string $type): SocialNetworkServiceInterface
    {
        return match ($type) {
            SocialNetworkType::LINKEDIN->toString() => new LinkedinSocialNetworkService(
                $this->linkedinApi,
                $this->userRepository,
                $this->linkedinSocialNetworkRepository,
                $this->typeRepository,
                $this->linkedinLoginUrl,
                $this->linkedinClientId,
                $this->callbackUrl,
                $this->frontUrl
            ),
            SocialNetworkType::FACEBOOK->toString() => new FacebookSocialNetworkService(
                $this->facebookApi,
                $this->userRepository,
                $this->facebookSocialNetworkRepository,
                $this->typeRepository,
                $this->facebookLoginUrl,
                $this->facebookClientId,
                $this->callbackUrl,
                $this->frontUrl
            ),
            SocialNetworkType::TWITTER->toString() => new TwitterSocialNetworkService(
                $this->twitterApi,
                $this->userRepository,
                $this->twitterSocialNetworkRepository,
                $this->typeRepository,
                $this->serializer,
                $this->validator,
                $this->validatorError,
                $this->twitterApiUrl,
                $this->twitterApiKey,
                $this->twitterApiSecret,
                $this->callbackUrl,
                $this->frontUrl,
                null
            ),
            default => new DefaultSocialNetworkService(
                $this->frontUrl
            ),
        };
    }
}
