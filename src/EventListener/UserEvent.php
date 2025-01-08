<?php

namespace App\EventListener;

use App\Entity\User;
use App\Repository\OrganizationRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener(event: Events::prePersist, priority: 0, connection: 'default')]
#[AsDoctrineListener(event: Events::postPersist, priority: 0, connection: 'default')]
#[AsDoctrineListener(event: Events::preUpdate, priority: 0, connection: 'default')]
readonly class UserEvent
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private OrganizationRepository $organizationRepository,
    ) {
    }

    #[NoReturn]
    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof User) {
            return;
        }

        $this->hashPassword($entity);
    }

    #[NoReturn]
    public function postPersist(PostPersistEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof User) {
            return;
        }

        $this->createOrganization($entity);
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

    private function hashPassword(User $user): void
    {
        if ($user->getPlainPassword()) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));
        }

        $user->eraseCredentials();
    }

    private function createOrganization(User $user): void
    {
        $workspace = $this->organizationRepository->create([
            'name' => sprintf("%s's Organization", $user->getName()),
            'logoUrl' => 'https://avatar.vercel.sh/personal.png',
            'admin' => $user,
        ]);

        $user->setActiveOrganization($workspace);
        $user->addOrganization($workspace);
    }
}
