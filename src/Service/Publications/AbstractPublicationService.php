<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Enum\PublicationStatus;
use App\Message\PublishScheduledPublicationsMessage;
use App\Repository\AbstractRepository;
use App\Repository\Publication\PublicationRepository;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class AbstractPublicationService
{
    public function __construct(
        private readonly AbstractRepository $publicationRepository,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function processPublicationError(array $publications, string $threadUuid, string $threadType, ?string $message, string $status): void
    {
        /** @var Publication $publication */
        foreach($publications as $publication) {
            $this->publicationRepository->update($publication, [
                'status' => $status,
                'statusMessage' => $message,
                'retry' => $publication->getRetry() + 1,
                'retryTime' => 3600,
            ]);
        }

        if ($status === PublicationStatus::RETRY->toString()) {
            $this->messageBus->dispatch(new PublishScheduledPublicationsMessage($threadUuid, $threadType), [
                new AmqpStamp('high', 0, []),
                new DelayStamp(3600000),
            ]);
        }
    }
}