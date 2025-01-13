<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Entity\SocialNetwork\LinkedinSocialNetwork;
use App\Repository\Publication\LinkedinPublicationRepository;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

class LinkedinPublicationService extends AbstractPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly LinkedinPublicationRepository $linkedinPublicationRepository,
        private readonly LinkedinSocialNetworkRepository $linkedinSocialNetworkRepository,
        private readonly PublicationService $publicationService,
    ) {
    }

    /**
     * @throws \Exception
     * @throws ExceptionInterface
     */
    public function create(PostPublications $postPublications): void
    {
        $socialNetwork = $this->linkedinSocialNetworkRepository->findOneByCriteria(['uuid' => $postPublications->socialNetworkUuid]);

        if (!$socialNetwork instanceof LinkedinSocialNetwork) {
            throw new \Exception('This social network does not exist');
        }

        $this->publicationService->save($postPublications, $socialNetwork, $this->linkedinPublicationRepository);
    }

    public function publish(array $publications)
    {
    }
}