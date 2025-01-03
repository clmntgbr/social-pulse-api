<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Publication\Publication;
use App\Entity\User;
use App\Enum\PublicationStatus;
use App\Enum\PublicationThreadType;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class PublicationExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security
    ) {}

    /**
     * @throws Exception
     */
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * @throws Exception
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Publication::class !== $resourceClass) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new Exception('You have to be authenticated.', 403);
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->join(sprintf('%s.socialNetwork', $rootAlias), 's');
        $queryBuilder->andWhere('s.organization = :organization');
        $queryBuilder->andWhere(sprintf('%s.status IN (:status)', $rootAlias));
        $queryBuilder->andWhere(sprintf('%s.threadType = :threadType', $rootAlias));
        $queryBuilder->setParameter('threadType', PublicationThreadType::PRIMARY);
        $queryBuilder->setParameter('organization', $user->getActiveOrganization());
        $queryBuilder->setParameter('status', [PublicationStatus::SCHEDULED->toString(), PublicationStatus::POSTED->toString(), PublicationStatus::FAILED->toString(), PublicationStatus::DRAFT->toString()]);
    }

    /**
     * @throws Exception
     */
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }
}