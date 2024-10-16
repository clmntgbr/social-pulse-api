<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Api\Controller\GetUser;
use App\ApiResource\Controller\GetUserAction;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
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
            controller: GetUserAction::class,
            paginationEnabled: false,
            normalizationContext: ['skip_null_values' => false, 'groups' => ['get_user']],
            read: false,
            name: 'me',
        ),
        new Get(
            normalizationContext: ['groups' => ['get_user']]
        ),
        new GetCollection(
            order: ['updatedAt' => 'ASC'],
            normalizationContext: ['skip_null_values' => false, 'groups' => ['get_user']]
        )
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, length: 36, unique: true)]
    #[Groups(['get_user', 'get_workspaces'])]
    private ?string $uuid;

    #[ORM\Column(length: 180)]
    #[Groups(['get_user', 'get_workspaces'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $plainPassword = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_user', 'get_workspaces'])]
    private ?string $givenName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_user', 'get_workspaces'])]
    private ?string $familyName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_user', 'get_workspaces'])]
    private ?string $avatarUrl = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $callback = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $state = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToOne(targetEntity: Workspace::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get_user'])]
    private ?Workspace $activeWorkspace = null;

    #[ORM\ManyToMany(targetEntity: Workspace::class, mappedBy: 'users', cascade: ['persist', 'remove'])]
    #[Groups(['get_user'])]
    private Collection $workspaces;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->workspaces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCallback(): ?string
    {
        return $this->callback;
    }

    public function setCallback(?string $callback): static
    {
        $this->callback = $callback;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, Workspace>
     */
    public function getWorkspaces(): Collection
    {
        return $this->workspaces;
    }

    public function addWorkspace(Workspace $workspace): static
    {
        if (!$this->workspaces->contains($workspace)) {
            $this->workspaces->add($workspace);
            $workspace->addUser($this);
        }

        return $this;
    }

    public function removeWorkspace(Workspace $workspace): static
    {
        if ($this->workspaces->removeElement($workspace)) {
            $workspace->removeUser($this);
        }

        return $this;
    }

    public function getActiveWorkspace(): ?Workspace
    {
        return $this->activeWorkspace;
    }

    public function setActiveWorkspace(?Workspace $activeWorkspace): static
    {
        $this->activeWorkspace = $activeWorkspace;

        return $this;
    }
}
