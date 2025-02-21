<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Entity\SocialNetwork\SocialNetwork;
use App\Enum\PublicationThreadType;
use App\Enum\SocialNetworkType;
use App\Message\PublishScheduledPublicationsMessage;
use App\Repository\AbstractRepository;
use App\Repository\Publication\FacebookPublicationRepository;
use App\Repository\Publication\InstagramPublicationRepository;
use App\Repository\Publication\LinkedinPublicationRepository;
use App\Repository\Publication\TwitterPublicationRepository;
use App\Repository\Publication\YoutubePublicationRepository;
use App\Service\ImageService;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class PublicationService
{
    public function __construct(
        private ImageService $imageService,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function save(PostPublications $postPublications, SocialNetwork $socialNetwork, AbstractRepository $repositoryRepository): void
    {
        $threadUuid = Uuid::uuid4()->toString();
        $threadUuids = [];

        foreach ($postPublications->publications as $publication) {
            $uuid = Uuid::uuid4()->toString();
            $threadType = $publication->threadType;

            if (SocialNetworkType::TWITTER !== $socialNetwork->getSocialNetworkType()->getName()) {
                $threadUuid = Uuid::uuid4()->toString();
                $threadType = PublicationThreadType::PRIMARY->toString();
            }

            $pictures = [];
            foreach ($publication->pictures as $picture) {
                $pictures[] = $this->imageService->saveBase64File('publications', $uuid, $picture);
            }

            /* @var FacebookPublicationRepository | LinkedinPublicationRepository | TwitterPublicationRepository | InstagramPublicationRepository | YoutubePublicationRepository $publicationRepository */
            $repositoryRepository->create([
                'content' => $publication->content,
                'uuid' => $uuid,
                'threadUuid' => $threadUuid,
                'threadType' => $threadType,
                'pictures' => $pictures,
                'socialNetwork' => $socialNetwork,
                'status' => $publication->status,
                'publishedAt' => $publication->publishedAt,
            ]);

            $threadUuids[] = ['uuid' => $threadUuid, 'publishedAt' => $publication->publishedAt];
        }

        foreach ($threadUuids as $item) {
            if ($item['publishedAt'] <= new \DateTime()) {
                $this->messageBus->dispatch(new PublishScheduledPublicationsMessage($item['uuid'], $socialNetwork->getSocialNetworkType()->getName()), [
                    new AmqpStamp('high', 0, []),
                ]);
            }
        }
    }
}
