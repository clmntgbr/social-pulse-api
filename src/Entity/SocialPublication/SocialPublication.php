<?php

namespace App\Entity\SocialPublication;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Traits\UuidTrait;
use App\Repository\SocialPublicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocialPublicationRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ],
    order: ['createdAt' => 'DESC']
)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'linkedinsocial_publication' => 'LinkedinSocialPublication',
    'twittersocial_publication' => 'TwitterSocialPublication',
    'facebooksocial_publication' => 'FacebookSocialPublication',
    'youtubesocial_publication' => 'YoutubeSocialPublication',
    'instagramsocial_publication' => 'InstagramSocialPublication',
])]
class SocialPublication
{
    use UuidTrait;

    #[ORM\Column(type: Types::STRING)]
    private ?string $socialPublicationType;

    public function __construct()
    {
        $this->initializeUuid();
    }

    public function getSocialPublicationType(): ?string
    {
        return $this->socialPublicationType;
    }

    public function setSocialPublicationType(string $socialPublicationType): static
    {
        $this->socialPublicationType = $socialPublicationType;

        return $this;
    }
}
