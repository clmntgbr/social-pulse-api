<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Entity\Publication\TwitterPublication;
use App\Entity\SocialNetwork\TwitterSocialNetwork;
use App\Enum\PublicationStatus;
use App\Enum\PublicationThreadType;
use App\Repository\Publication\TwitterPublicationRepository;
use App\Repository\SocialNetwork\TwitterSocialNetworkRepository;
use App\Service\ImageService;
use App\Service\TwitterApi;
use Symfony\Component\Messenger\MessageBusInterface;

class TwitterPublicationService extends AbstractPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly TwitterPublicationRepository $twitterPublicationRepository,
        private readonly TwitterSocialNetworkRepository $twitterSocialNetworkRepository,
        private readonly PublicationService $publicationService,
        private readonly TwitterApi $twitterApi,
        private readonly ImageService $imageService,
        private readonly MessageBusInterface $messageBus,
        private readonly string $projectRoot,
    ) {
        parent::__construct($this->twitterPublicationRepository, $this->messageBus);
    }

    public function create(PostPublications $postPublications): void
    {
        $socialNetwork = $this->twitterSocialNetworkRepository->findOneByCriteria(['uuid' => $postPublications->socialNetworkUuid]);

        if (!$socialNetwork instanceof TwitterSocialNetwork) {
            throw new \Exception('This social network does not exist');
        }

        $this->publicationService->save($postPublications, $socialNetwork, $this->twitterPublicationRepository);
    }

    /** @var TwitterPublication[] */
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

                try {
                    $twitterMedia = $this->twitterApi->uploadMedia($publication->getSocialNetwork(), sprintf('%s/public/%s', $this->projectRoot, $media));
                } catch (\Exception $exception) {
                    $this->processPublicationError($publications, $publication->getThreadUuid(), $publication->getSocialNetwork()->getSocialNetworkType()->getName(), $exception->getMessage(), PublicationStatus::RETRY->toString());

                    return;
                }

                if (!$twitterMedia) {
                    $this->processPublicationError($publications, $publication->getThreadUuid(), $publication->getSocialNetwork()->getSocialNetworkType()->getName(), 'UploadMedia error', PublicationStatus::RETRY->toString());

                    return;
                }

                $this->imageService->delete(sprintf('%s/public/%s', $this->projectRoot, $media));
                $mediaIds[] = $twitterMedia->mediaId;
            }

            $payload = [
                'text' => $publication->getContent(),
            ];

            if (!empty($mediaIds)) {
                $payload['media'] = ['media_ids' => $mediaIds];
            }

            if ($primaryId) {
                $payload['reply']['in_reply_to_tweet_id'] = $primaryId;
            }

            try {
                $response = $this->twitterApi->tweet($twitterSocialNetwork, $payload);
            } catch (\Exception $exception) {
                $this->processPublicationError($publications, $publication->getThreadUuid(), $publication->getSocialNetwork()->getSocialNetworkType()->getName(), $exception->getMessage(), PublicationStatus::RETRY->toString());

                return;
            }

            if (PublicationThreadType::PRIMARY === $publication->getThreadType()) {
                $primaryId = $response->id;
            }

            $this->twitterPublicationRepository->update($publication, [
                'publicationId' => $response->id,
                'status' => PublicationStatus::POSTED->toString(),
                'statusMessage' => null,
                'publishedAt' => new \DateTime(),
            ]);
        }
    }
}
