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
        $publication = $this->publicationRepository->findOneBy(['uuid' => $publishScheduledPublicationsMessage->getUuid()]);

        if (!$publication instanceof Publication) {
            return;
        }

        $publications = $this->publicationRepository->findBy(
            ['threadUuid' => $publication->getThreadUuid()],
            ['id' => 'ASC']
        );

        if (empty($publications)) {
            return;
        }

        $publicationService = $this->publicationServiceFactory->getService($publication->getPublicationType());
        $publicationService->publish($publications);

        $this->publicationRepository->update($publication, [
            'status' => PublicationStatus::POSTED->toString(),
        ]);
    }
}