<?php

namespace App\Repository;

use App\Dto\Api\UserRegister;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $passwordAuthenticatedUser, string $newHashedPassword): void
    {
        if (!$passwordAuthenticatedUser instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $passwordAuthenticatedUser::class));
        }

        $passwordAuthenticatedUser->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($passwordAuthenticatedUser);
        $this->getEntityManager()->flush();
    }

    public function updateOrCreate(array $searchPayload, array $updatePayload): User
    {
        $account = $this->findOneByCriteria($searchPayload);
        if ($account === null) {
            $account = new User();
        }

        $this->update($account, $updatePayload);

        return $account;
    }

    public function create(UserRegister $userRegister): User
    {
        $user = new User();
        $user
            ->setEmail($userRegister->email)
            ->setPlainPassword($userRegister->password);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }
}
