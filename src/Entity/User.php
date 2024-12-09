<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\ApiResource\PatchUserActiveOrganizationAction;
use App\Entity\Traits\UuidTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/me',
            normalizationContext: ['groups' => ['user:get']],
        ),
        new Patch(
            uriTemplate: '/users/active_organisation/{uuid}',
            controller: PatchUserActiveOrganizationAction::class,
        )
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;
    use UuidTrait;

    #[ORM\Column(length: 180)]
    #[Groups(['user:get', "organizations:get"])]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $plainPassword = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['user:get', "organizations:get"])]
    private ?string $givenName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['user:get', "organizations:get"])]
    private ?string $familyName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['user:get', "organizations:get"])]
    private ?string $avatarUrl = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $socialNetworksState = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $socialNetworksCallbackPath = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Organization $activeOrganization = null;

    #[ORM\ManyToMany(targetEntity: Organization::class, mappedBy: 'users', cascade: ['persist', 'remove'])]
    private Collection $organizations;

    public function __construct()
    {
        $this->initializeUuid();
        $this->organizations = new ArrayCollection();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $password): static
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
         $this->plainPassword = null;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(?string $givenName): static
    {
        $this->givenName = $givenName;

        return $this;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    #[Groups(['user:get', "organizations:get"])]
    public function getName(): ?string
    {
        return sprintf('%s %s', $this->givenName, $this->familyName);
    }

    public function setFamilyName(?string $familyName): static
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function getActiveOrganization(): ?Organization
    {
        return $this->activeOrganization;
    }

    public function setActiveOrganization(?Organization $activeOrganization): static
    {
        $this->activeOrganization = $activeOrganization;

        return $this;
    }

    /**
     * @return Collection<int, Organization>
     */
    public function getOrganizations(): Collection
    {
        return $this->organizations;
    }

    public function addOrganization(Organization $organization): static
    {
        if (!$this->organizations->contains($organization)) {
            $this->organizations->add($organization);
            $organization->addUser($this);
        }

        return $this;
    }

    public function removeOrganization(Organization $organization): static
    {
        if ($this->organizations->removeElement($organization)) {
            $organization->removeUser($this);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isOneOfMine(string $organizationUuid): bool
    {
        return $this->organizations->exists(function ($key, $organization) use ($organizationUuid) {
            return $organization->getUuid() === $organizationUuid;
        });
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSocialNetworksState(): ?string
    {
        return $this->socialNetworksState;
    }

    public function setSocialNetworksState(?string $socialNetworksState): User
    {
        $this->socialNetworksState = $socialNetworksState;
        return $this;
    }

    public function getSocialNetworksCallbackPath(): ?string
    {
        return $this->socialNetworksCallbackPath;
    }

    public function setSocialNetworksCallbackPath(?string $socialNetworksCallbackPath): User
    {
        $this->socialNetworksCallbackPath = $socialNetworksCallbackPath;
        return $this;
    }
}
