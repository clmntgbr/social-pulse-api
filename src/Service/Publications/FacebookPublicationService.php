<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Entity\SocialNetwork\FacebookSocialNetwork;
use App\Repository\Publication\FacebookPublicationRepository;
use App\Repository\SocialNetwork\FacebookSocialNetworkRepository;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

class FacebookPublicationService extends AbstractPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly FacebookPublicationRepository $facebookPublicationRepository,
        private readonly FacebookSocialNetworkRepository $facebookSocialNetworkRepository,
        private readonly PublicationService $publicationService,
    ) {
    }

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function create(PostPublications $postPublications): void
    {
        $socialNetwork = $this->facebookSocialNetworkRepository->findOneByCriteria(['uuid' => $postPublications->socialNetworkUuid]);

        if (!$socialNetwork instanceof FacebookSocialNetwork) {
            throw new \Exception('This social network does not exist');
        }

        $this->publicationService->save($postPublications, $socialNetwork, $this->facebookPublicationRepository);
    }

    public function publish(array $publications)
    {
    }
}