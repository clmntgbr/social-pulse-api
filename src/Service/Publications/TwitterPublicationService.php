<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Entity\SocialNetwork\TwitterSocialNetwork;
use App\Repository\Publication\TwitterPublicationRepository;
use App\Repository\SocialNetwork\TwitterSocialNetworkRepository;

readonly class TwitterPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly TwitterPublicationRepository $twitterPublicationRepository,
        private readonly TwitterSocialNetworkRepository $twitterSocialNetworkRepository,
        private readonly PublicationService $publicationService,
    ) {
    }

    public function create(PostPublications $postPublications): void
    {
        $socialNetwork = $this->twitterSocialNetworkRepository->findOneByCriteria(['uuid' => $postPublications->socialNetworkUuid]);

        if (!$socialNetwork instanceof TwitterSocialNetwork) {
            throw new \Exception('This social network does not exist');
        }

        $this->publicationService->publish($postPublications, $socialNetwork, $this->twitterPublicationRepository);
    }
}
