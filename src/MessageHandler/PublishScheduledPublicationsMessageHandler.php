<?php

namespace App\MessageHandler;

use App\Entity\Publication\Publication;
use App\Enum\PublicationStatus;
use App\Message\PublishScheduledPublicationsMessage;
use App\Repository\Publication\PublicationRepository;
use App\Service\Publications\PublicationServiceFactory;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PublishScheduledPublicationsMessageHandler
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository,
        private readonly PublicationServiceFactory $publicationServiceFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(PublishScheduledPublicationsMessage $publishScheduledPublicationsMessage): void
    {
        $this->logger->info(json_encode([
            'threadUuid' => $publishScheduledPublicationsMessage->getUuid(),
            'socialNetworkType' => $publishScheduledPublicationsMessage->getSocialNetworkType(),
        ]));
        
        $publications = $this->publicationRepository->findBy(
            ['threadUuid' => $publishScheduledPublicationsMessage->getUuid()],
            ['id' => 'ASC']
        );

        if (empty($publications)) {
            return;
        }

        $publicationService = $this->publicationServiceFactory->getService($publishScheduledPublicationsMessage->getSocialNetworkType());

        if ($publications[0] && $publications[0]->getRetry() >= 3) {
            $publicationService->processPublicationError($publications, $publishScheduledPublicationsMessage->getUuid(), $publishScheduledPublicationsMessage->getSocialNetworkType(), 'Too much retry.', PublicationStatus::FAILED->toString());
            return;
        }

        $publicationService->publish($publications);
    }
}