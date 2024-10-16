<?php

namespace App\EventListener;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WorkspaceRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener(event: Events::prePersist, priority: 0, connection: 'default')]
#[AsDoctrineListener(event: Events::preUpdate, priority: 0, connection: 'default')]
readonly class UserEvent
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private WorkspaceRepository $workspaceRepository,
        private UserRepository $userRepository
    ) {}

    #[NoReturn]
    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof User) {
            return;
        }

        $this->hashPassword($entity);
        $this->createWorkspace($entity);
    }

    #[NoReturn]
    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof User) {
            return;
        }

        $this->hashPassword($entity);
    }

    function hashPassword(User $user): void
    {
        if ($user->getPlainPassword()) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));
        }

        $user->eraseCredentials();
    }

    function createWorkspace(User $user): User
    {
        $workspace = $this->workspaceRepository->create([
            'label' => 'My Workspace',
            'logoUrl' => 'https://avatar.vercel.sh/personal.png',
        ]);

        $user->setActiveWorkspace($workspace);
        $user->addWorkspace($workspace);

        return $user;
    }
}