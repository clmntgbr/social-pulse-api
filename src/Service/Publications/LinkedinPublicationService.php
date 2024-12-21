<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Entity\SocialNetwork\LinkedinSocialNetwork;
use App\Repository\Publication\LinkedinPublicationRepository;
use App\Repository\Publication\PublicationRepository;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use App\Service\ImageService;
use Ramsey\Uuid\Uuid;

readonly class LinkedinPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly LinkedinPublicationRepository $publicationRepository,
        private readonly LinkedinSocialNetworkRepository $socialNetworkRepository,
        private readonly ImageService $imageService
    ){}

    /**
     * @throws \Exception
     */
    public function create(PostPublications $postPublications): void
    {
        $socialNetwork = $this->socialNetworkRepository->findOneByCriteria(['uuid' => $postPublications->socialNetworkUuid]);

        if (!$socialNetwork instanceof LinkedinSocialNetwork) {
            throw new \Exception('This social network does not exist');
        }

        $threadUuid = Uuid::uuid4()->toString();
        foreach ($postPublications->publications as $publication) {
            $uuid = Uuid::uuid4()->toString();

            $pictures = [];
            foreach ($publication->pictures as $picture) {
                $pictures[] = $this->imageService->saveBase64File('publications', $uuid, $picture);
            }

            $this->publicationRepository->create([
                'content' => $publication->content,
                'uuid' => $uuid,
                'threadUuid' => $threadUuid,
                'threadType' => $publication->threadType,
                'pictures' => $pictures,
                'socialNetwork' => $socialNetwork,
                'status' => $publication->status,
                'publishedAt' => $publication->publishedAt,
            ]);
        }
    }
}