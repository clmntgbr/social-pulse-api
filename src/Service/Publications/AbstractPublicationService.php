<?php

namespace App\Service\Publications;

use App\Enum\PublicationStatus;
use App\Message\PublishScheduledPublicationsMessage;
use App\Repository\AbstractRepository;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class AbstractPublicationService
{
    public function __construct(
    ) {
    }
}
