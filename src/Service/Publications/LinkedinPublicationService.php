<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Entity\SocialNetwork\LinkedinSocialNetwork;
use App\Repository\Publication\LinkedinPublicationRepository;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

readonly class LinkedinPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly LinkedinPublicationRepository $publicationRepository,
        private readonly LinkedinSocialNetworkRepository $socialNetworkRepository,
        private readonly PublicationService $service,
    ) {
    }

    /**
     * @throws \Exception
     * @throws ExceptionInterface
     */
    public function create(PostPublications $postPublications): void
    {
        $socialNetwork = $this->socialNetworkRepository->findOneByCriteria(['uuid' => $postPublications->socialNetworkUuid]);

        if (!$socialNetwork instanceof LinkedinSocialNetwork) {
            throw new \Exception('This social network does not exist');
        }

        $this->service->publish($postPublications, $socialNetwork, $this->publicationRepository);
    }
}
