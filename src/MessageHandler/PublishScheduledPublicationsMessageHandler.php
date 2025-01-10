<?php

namespace App\MessageHandler;

use App\Entity\Publication\Publication;
use App\Enum\PublicationStatus;
use App\Message\PublishScheduledPublicationsMessage;
use App\Repository\Publication\PublicationRepository;
use App\Service\Publications\PublicationServiceFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PublishScheduledPublicationsMessageHandler
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository,
        private readonly PublicationServiceFactory $publicationServiceFactory
    ) {
    }

    public function __invoke(PublishScheduledPublicationsMessage $publishScheduledPublicationsMessage): void
    {
       $publications = $this->publicationRepository->findBy(
            ['threadUuid' => $publishScheduledPublicationsMessage->getUuid()],
            ['id' => 'ASC']
        );

        if (empty($publications)) {
            return;
        }

        $publicationService = $this->publicationServiceFactory->getService($publishScheduledPublicationsMessage->getSocialNetworkType());
        $publicationService->publish($publications);
    }
}