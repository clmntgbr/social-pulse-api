<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Workspace;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::prePersist, priority: 0, connection: 'default')]
readonly class WorkspaceEvent
{
    public function __construct(
        private Security $security
    ) {}

    #[NoReturn]
    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof Workspace) {
            return;
        }

        $user = $this->security->getUser();
        if ($user instanceof User) {
            $entity->addUser($user);
        }
    }
}