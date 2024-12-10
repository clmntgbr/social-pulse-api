<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\ApiResource\PostOrganizationsAction;
use App\Entity\SocialNetwork\SocialNetwork;
use App\Entity\Traits\UuidTrait;
use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/organization',
            normalizationContext: ['skip_null_values' => false, 'groups' => ['organization:get', 'default']],
        ),
        new GetCollection(
            uriTemplate: '/organizations',
            normalizationContext: ['skip_null_values' => false, 'groups' => ['organizations:get', 'default', 'social-networks:get']],
        ),
        new Post(
            uriTemplate: '/organizations',
            controller: PostOrganizationsAction::class,
        ),
    ]
)]
class Organization
{
    use UuidTrait;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(["organization:get", "organizations:get"])]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(["organization:get", "organizations:get"])]
    private ?string $logoUrl = null;

    #[ORM\OneToMany(targetEntity: SocialNetwork::class, mappedBy: 'organization', cascade: ['remove'])]
    #[Groups(['social-networks:get'])]
    private Collection $socialNetworks;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'organizations')]
    #[Groups(["organizations:get"])]
    private Collection $users;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["organizations:get"])]
    private User $admin;

    public function __construct()
    {
        $this->initializeUuid();
        $this->socialNetworks = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(string $logoUrl): static
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    /**
     * @return Collection<int, SocialNetwork>
     */
    public function getSocialNetworks(): Collection
    {
        return $this->socialNetworks;
    }

    public function addSocialNetwork(SocialNetwork $socialNetwork): static
    {
        if (!$this->socialNetworks->contains($socialNetwork)) {
            $this->socialNetworks->add($socialNetwork);
            $socialNetwork->setOrganization($this);
        }

        return $this;
    }

    public function removeSocialNetwork(SocialNetwork $socialNetwork): static
    {
        if ($this->socialNetworks->removeElement($socialNetwork)) {
            // set the owning side to null (unless already changed)
            if ($socialNetwork->getOrganization() === $this) {
                $socialNetwork->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): static
    {
        $this->admin = $admin;

        return $this;
    }
}
