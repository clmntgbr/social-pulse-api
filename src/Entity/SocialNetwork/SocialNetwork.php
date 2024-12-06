<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
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
        new GetCollection(),
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
    #[Groups(["organizations:get"])]
    private ?string $socialNetworkId = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(["organizations:get"])]
    private ?string $socialNetworkType;

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

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

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
}
