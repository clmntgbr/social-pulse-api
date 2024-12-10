<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\GetSocialNetworksCallbackAction;
use App\ApiResource\GetSocialNetworksConnectAction;
use App\Entity\Organization;
use App\Entity\Traits\UuidTrait;
use App\Repository\SocialNetwork\SocialNetworkRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SocialNetworkRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['social-networks:get', 'default']],
        ),
        new Get(
            uriTemplate: '/social_networks/{platform}/connect',
            controller: GetSocialNetworksConnectAction::class,
        ),
        new Get(
            uriTemplate: '/social_networks/{platform}/callback',
            controller: GetSocialNetworksCallbackAction::class,
        )
    ],
    order: ['createdAt' => 'DESC']
)]
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

    #[ORM\Column(type: Types::STRING, unique: false)]
    private ?string $socialNetworkId = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(["social-networks:get"])]
    private ?string $socialNetworkType;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(["social-networks:get"])]
    private int $followers = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(["social-networks:get"])]
    private int $followings = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(["social-networks:get"])]
    private int $shares = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(["social-networks:get"])]
    private int $comments = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(["social-networks:get"])]
    private int $likes = 0;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(["social-networks:get"])]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["social-networks:get"])]
    private ?string $avatarUrl;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(["social-networks:get"])]
    private ?string $username;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(["social-networks:get"])]
    private ?string $name;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(["social-networks:get"])]
    private ?string $email;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $token;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'socialNetworks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;

    public function __construct()
    {
        $this->initializeUuid();
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

    public function getSocialNetworkType(): ?string
    {
        return $this->socialNetworkType;
    }

    public function setSocialNetworkType(string $socialNetworkType): static
    {
        $this->socialNetworkType = $socialNetworkType;

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
}
