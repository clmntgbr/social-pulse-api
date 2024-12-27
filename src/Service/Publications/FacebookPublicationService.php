<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Entity\SocialNetwork\FacebookSocialNetwork;
use App\Message\PublishScheduledPublicationsMessage;
use App\Repository\Publication\FacebookPublicationRepository;
use App\Repository\SocialNetwork\FacebookSocialNetworkRepository;
use App\Service\ImageService;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class FacebookPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly FacebookPublicationRepository $publicationRepository,
        private readonly FacebookSocialNetworkRepository $socialNetworkRepository,
        private readonly PublicationService $service
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function create(PostPublications $postPublications): void
    {
        $socialNetwork = $this->socialNetworkRepository->findOneByCriteria(['uuid' => $postPublications->socialNetworkUuid]);

        if (!$socialNetwork instanceof FacebookSocialNetwork) {
            throw new \Exception('This social network does not exist');
        }

        $this->service->publish($postPublications, $socialNetwork, $this->publicationRepository);
    }
}