<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Entity\SocialNetwork\SocialNetwork;
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
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function publish(PostPublications $postPublications, SocialNetwork $socialNetwork, AbstractRepository $publicationRepository): void
    {
        $threadUuid = Uuid::uuid4()->toString();

        foreach ($postPublications->publications as $publication) {
            $uuid = Uuid::uuid4()->toString();

            $pictures = [];
            foreach ($publication->pictures as $picture) {
                $pictures[] = $this->imageService->saveBase64File('publications', $uuid, $picture);
            }

            /** @var FacebookPublicationRepository | LinkedinPublicationRepository | TwitterPublicationRepository | InstagramPublicationRepository | YoutubePublicationRepository $publicationRepository */
            $publicationRepository->create([
                'content' => $publication->content,
                'uuid' => $uuid,
                'threadUuid' => $threadUuid,
                'threadType' => $publication->threadType,
                'pictures' => $pictures,
                'socialNetwork' => $socialNetwork,
                'status' => $publication->status,
                'publishedAt' => $publication->publishedAt,
            ]);

            if ($publication->publishedAt <= new \DateTime()) {
                $this->messageBus->dispatch(new PublishScheduledPublicationsMessage($uuid), [
                    new AmqpStamp('high', 0, []),
                ]);
            }
        }
    }
}