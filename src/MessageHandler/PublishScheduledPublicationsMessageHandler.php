<?php

namespace App\MessageHandler;

use App\Entity\Publication\Publication;
use App\Enum\PublicationStatus;
use App\Message\PublishScheduledPublicationsMessage;
use App\Repository\Publication\PublicationRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PublishScheduledPublicationsMessageHandler
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository
    ) {}

    public function __invoke(PublishScheduledPublicationsMessage $message): void
    {
        $publication = $this->publicationRepository->findOneBy(['uuid' => $message->getUuid()]);

        if (!$publication instanceof Publication) {
            return;
        }

        $this->publicationRepository->update($publication, [
            'status' => PublicationStatus::POSTED->toString(),
        ]);
    }
}
