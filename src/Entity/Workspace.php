<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\ApiResource\Controller\GetUserAction;
use App\ApiResource\Controller\PostUserActiveWorkspaceAction;
use App\ApiResource\Controller\GetWorkspaceAction;
use App\Entity\Traits\UuidTrait;
use App\Repository\WorkspaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: WorkspaceRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['get_workspaces']],
        ),
        new Post(
            denormalizationContext: ['groups' => ['post_workspace']]
        ),
        new Get(
            uriTemplate: '/workspace',
            controller: GetWorkspaceAction::class,
            paginationEnabled: false,
            normalizationContext: ['skip_null_values' => false, 'groups' => ['get_workspaces']],
            read: false,
            name: 'get_active_workspace',
        )
    ]
)]
class Workspace
{
    use UuidTrait;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['get_user', 'get_workspaces', 'post_workspace'])]
    private ?string $label = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['get_user', 'get_workspaces', 'post_workspace'])]
    private ?string $logoUrl = null;

    #[ORM\OneToMany(targetEntity: SocialAccount::class, mappedBy: 'workspace')]
    #[Groups(['get_workspaces'])]
    private Collection $socialAccounts;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'workspaces')]
    #[Groups(['get_workspaces'])]
    private Collection $users;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->socialAccounts = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

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
     * @return Collection<int, SocialAccount>
     */
    public function getSocialAccounts(): Collection
    {
        return $this->socialAccounts;
    }

    public function addSocialAccount(SocialAccount $socialAccount): static
    {
        if (!$this->socialAccounts->contains($socialAccount)) {
            $this->socialAccounts->add($socialAccount);
            $socialAccount->setWorkspace($this);
        }

        return $this;
    }

    public function removeSocialAccount(SocialAccount $socialAccount): static
    {
        if ($this->socialAccounts->removeElement($socialAccount)) {
            // set the owning side to null (unless already changed)
            if ($socialAccount->getWorkspace() === $this) {
                $socialAccount->setWorkspace(null);
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
}
