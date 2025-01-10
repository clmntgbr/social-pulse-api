<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Dto\Twitter\TwitterTweet;
use App\Entity\Publication\TwitterPublication;
use App\Entity\SocialNetwork\TwitterSocialNetwork;
use App\Enum\PublicationStatus;
use App\Enum\PublicationThreadType;
use App\Repository\Publication\TwitterPublicationRepository;
use App\Repository\SocialNetwork\TwitterSocialNetworkRepository;
use App\Service\ImageService;
use App\Service\TwitterApi;
use Ramsey\Uuid\Uuid;

readonly class TwitterPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly TwitterPublicationRepository $twitterPublicationRepository,
        private readonly TwitterSocialNetworkRepository $twitterSocialNetworkRepository,
        private readonly PublicationService $publicationService,
        private readonly TwitterApi $twitterApi,
        private readonly ImageService $imageService,
        private readonly string $projectRoot
    ) {
    }

    public function create(PostPublications $postPublications): void
    {
        $socialNetwork = $this->twitterSocialNetworkRepository->findOneByCriteria(['uuid' => $postPublications->socialNetworkUuid]);

        if (!$socialNetwork instanceof TwitterSocialNetwork) {
            throw new \Exception('This social network does not exist');
        }

        $this->publicationService->save($postPublications, $socialNetwork, $this->twitterPublicationRepository);
    }

    /** @var TwitterPublication[] $publications */
    public function publish(array $publications)
    {
        $twitterSocialNetwork = null;
        $primaryId = null;
        
        /** @var TwitterPublication $publication */
        foreach ($publications as $publication) {
            $twitterSocialNetwork = $publication->getSocialNetwork();
            $mediaIds = [];
            foreach ($publication->getPictures() as $picture) {
                $media = $this->imageService->downloadTmp($picture);
                $twitterMedia = $this->twitterApi->uploadMedia($publication->getSocialNetwork(), sprintf('%s/public/%s', $this->projectRoot, $media));

                if (!$twitterMedia) {
                    continue;
                }
                
                $this->imageService->delete(sprintf('%s/public/%s', $this->projectRoot, $media));
                $mediaIds[] = $twitterMedia->mediaId;
            }

            $payload = [
                'text' => Uuid::uuid4()->toString(),
            ];

            if (!empty($mediaIds)) {
                $payload['media'] = ['media_ids' => $mediaIds];
            }

            if ($primaryId) {
                $payload['reply']['in_reply_to_tweet_id'] = $primaryId;
            }

            $response = $this->twitterApi->tweet($twitterSocialNetwork, $payload);

            if (!$response instanceof TwitterTweet) {
                $this->twitterPublicationRepository->update($publication, [
                    'status' => PublicationStatus::RETRY->toString(),
                    'statusMessage' => $response,
                    'retry' => $publication->getRetry() + 1,
                    'retryTime' => 3600,
                ]);
                continue;
            }
            
            if ($publication->getThreadType() === PublicationThreadType::PRIMARY) {
                $primaryId = $response->id;
            }

            $this->twitterPublicationRepository->update($publication, [
                'status' => PublicationStatus::POSTED->toString(),
            ]);
        }
    }
}