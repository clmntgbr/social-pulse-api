<?php

namespace App\EventListener;

use App\Entity\SocialAccount;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::prePersist, priority: 0, connection: 'default')]
readonly class SocialAccountEvent
{
    public function __construct(
        private Security $security
    ) {}

    #[NoReturn]
    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof SocialAccount) {
            return;
        }

//        dd($entity, $this->security->getUser());
//
//        $user = $this->security->getUser();
//        if ($user instanceof User) {
//            $entity->setWorkspace($user->getActiveWorkspace());
//        }
    }
}