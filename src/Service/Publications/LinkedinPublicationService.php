<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Entity\SocialNetwork\LinkedinSocialNetwork;
use App\Message\PublishScheduledPublicationsMessage;
use App\Repository\Publication\LinkedinPublicationRepository;
use App\Repository\Publication\PublicationRepository;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use App\Service\ImageService;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class LinkedinPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly LinkedinPublicationRepository $publicationRepository,
        private readonly LinkedinSocialNetworkRepository $socialNetworkRepository,
        private readonly PublicationService $service
    ){}

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