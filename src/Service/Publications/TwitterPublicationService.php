<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Entity\SocialNetwork\TwitterSocialNetwork;
use App\Repository\Publication\TwitterPublicationRepository;
use App\Repository\SocialNetwork\TwitterSocialNetworkRepository;

readonly class TwitterPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly TwitterPublicationRepository $publicationRepository,
        private readonly TwitterSocialNetworkRepository $socialNetworkRepository,
        private readonly PublicationService $service
    ){}

    public function create(PostPublications $postPublications): void
    {
        $socialNetwork = $this->socialNetworkRepository->findOneByCriteria(['uuid' => $postPublications->socialNetworkUuid]);

        if (!$socialNetwork instanceof TwitterSocialNetwork) {
            throw new \Exception('This social network does not exist');
        }

        $this->service->publish($postPublications, $socialNetwork, $this->publicationRepository);
    }
}