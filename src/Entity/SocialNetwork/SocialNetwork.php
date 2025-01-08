<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\ApiResource\GetSocialNetworksCallbackAction;
use App\ApiResource\GetSocialNetworksConnectAction;
use App\ApiResource\PostSocialNetworksValidateAction;
use App\Entity\Organization;
use App\Entity\Publication\Publication;
use App\Entity\Traits\UuidTrait;
use App\Enum\SocialNetworkStatus;
use App\Repository\SocialNetwork\SocialNetworkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SocialNetworkRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['social-networks:get', 'social-networks-type:get', 'default']],
        ),
        new Get(
            uriTemplate: '/social_networks/{platform}/connect',
            controller: GetSocialNetworksConnectAction::class,
        ),
        new Get(
            uriTemplate: '/social_networks/{platform}/callback',
            controller: GetSocialNetworksCallbackAction::class,
        ),
        new Post(
            uriTemplate: '/social_networks/validate/{validate}',
            controller: PostSocialNetworksValidateAction::class,
        ),
    ],
    order: ['createdAt' => 'DESC', 'name' => 'ASC'],
)]
#[ApiFilter(SearchFilter::class, properties: ['validate' => 'exact', 'status' => 'exact'])]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'linkedin_social_network' => 'LinkedinSocialNetwork',
    'twitter_social_network' => 'TwitterSocialNetwork',
    'facebook_social_network' => 'FacebookSocialNetwork',
    'youtube_social_network' => 'YoutubeSocialNetwork',
    'instagram_social_network' => 'InstagramSocialNetwork',
])]
class SocialNetwork
{
    use UuidTrait;
    use TimestampableEntity;
    public $createdAt;
    public $updatedAt;

    #[ORM\Column(type: Types::STRING, unique: false)]
    private ?string $socialNetworkId = null;

    #[ORM\ManyToOne(targetEntity: Type::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['social-networks-type:get'])]
    private ?Type $socialNetworkType;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['social-networks:get'])]
    private int $followers = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['social-networks:get'])]
    private int $followings = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['social-networks:get'])]
    private int $shares = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['social-networks:get'])]
    private int $comments = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['social-networks:get'])]
    private int $likes = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['social-networks:get'])]
    private ?int $maxCharacter = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['social-networks:get'])]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['social-networks:get'])]
    private ?string $avatarUrl;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['social-networks:get'])]
    private ?string $username;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['social-networks:get'])]
    private ?string $name;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['social-networks:get'])]
    private ?string $email;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $token;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['social-networks:get'])]
    private ?string $status;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['social-networks:get'])]
    private ?string $validate;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'socialNetworks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;

    #[ORM\OneToMany(targetEntity: Publication::class, mappedBy: 'socialNetwork', cascade: ['remove'])]
    private Collection $publications;

    public function __construct()
    {
        $this->initializeUuid();
        $this->status = SocialNetworkStatus::TEMPORARY->toString();
        $this->publications = new ArrayCollection();
    }

    #[Groups(['default'])]
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    #[Groups(['default'])]
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function getSocialNetworkId(): ?string
    {
        return $this->socialNetworkId;
    }

    public function setSocialNetworkId(string $socialNetworkId): static
    {
        $this->socialNetworkId = $socialNetworkId;

        return $this;
    }

    public function getFollowers(): ?int
    {
        return $this->followers;
    }

    public function setFollowers(int $followers): static
    {
        $this->followers = $followers;

        return $this;
    }

    public function getFollowings(): ?int
    {
        return $this->followings;
    }

    public function setFollowings(int $followings): static
    {
        $this->followings = $followings;

        return $this;
    }

    public function getShares(): ?int
    {
        return $this->shares;
    }

    public function setShares(int $shares): static
    {
        $this->shares = $shares;

        return $this;
    }

    public function getComments(): ?int
    {
        return $this->comments;
    }

    public function setComments(int $comments): static
    {
        $this->comments = $comments;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): static
    {
        $this->likes = $likes;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getValidate(): ?string
    {
        return $this->validate;
    }

    public function setValidate(?string $validate): static
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): static
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setSocialNetwork($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): static
    {
        if ($this->publications->removeElement($publication) && $publication->getSocialNetwork() === $this) {
            $publication->setSocialNetwork(null);
        }

        return $this;
    }

    public function getSocialNetworkType(): ?Type
    {
        return $this->socialNetworkType;
    }

    public function setSocialNetworkType(?Type $socialNetworkType): static
    {
        $this->socialNetworkType = $socialNetworkType;

        return $this;
    }

    public function getMaxCharacter(): ?int
    {
        return $this->maxCharacter;
    }

    public function setMaxCharacter(?int $maxCharacter): static
    {
        $this->maxCharacter = $maxCharacter;

        return $this;
    }
}
