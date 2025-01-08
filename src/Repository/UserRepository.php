<?php

namespace App\Repository;

use App\Dto\Api\UserRegister;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    public function __construct()
    {
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function updateOrCreate(array $searchPayload, array $updatePayload): User
    {
        $account = $this->findOneByCriteria($searchPayload);
        if (!$account) {
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
