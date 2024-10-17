<?php

namespace App\EventListener;

use App\Entity\SocialAccount;
use App\Enum\SocialAccountStatus;
use App\Message\DeleteTemporarySocialAccount;
use App\Service\OpenSslEncryption;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsDoctrineListener(event: Events::prePersist, priority: 0, connection: 'default')]
#[AsDoctrineListener(event: Events::postLoad, priority: 0, connection: 'default')]
readonly class SocialAccountEvent
{
    public function __construct(
        private MessageBusInterface $bus,
        private OpenSslEncryption $encryption
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof SocialAccount) {
            return;
        }

        if ($entity->getStatus() === SocialAccountStatus::TEMPORARY->toString()) {
            $this->bus->dispatch(new DeleteTemporarySocialAccount($entity->getUuid()), [
                new AmqpStamp('low', AMQP_NOPARAM, []),
                new DelayStamp(600000), // Temporary Social Account will be deleted after 10 minutes
            ]);
        }

        // It will encrypt all tokens for the DB
        $entity->setToken($this->encryption->encrypt($entity->getToken()));
        $entity->setTokenSecret($this->encryption->encrypt($entity->getTokenSecret()));
        $entity->setBearerToken($this->encryption->encrypt($entity->getBearerToken()));
        $entity->setRefreshToken($this->encryption->encrypt($entity->getRefreshToken()));
    }

    public function postLoad(PostLoadEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof SocialAccount) {
            return;
        }

        // It will decrypt all tokens on load
        $entity->setToken($this->encryption->encrypt($entity->getToken()));
        $entity->setTokenSecret($this->encryption->encrypt($entity->getTokenSecret()));
        $entity->setBearerToken($this->encryption->encrypt($entity->getBearerToken()));
        $entity->setRefreshToken($this->encryption->encrypt($entity->getRefreshToken()));
    }
}