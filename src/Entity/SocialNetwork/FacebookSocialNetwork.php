<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SocialNetwork\FacebookSocialNetworkRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FacebookSocialNetworkRepository::class)]
#[ApiResource(
    operations: []
)]
class FacebookSocialNetwork extends SocialNetwork
{
    public function __construct()
    {
        parent::__construct();
        $this->setMaxCharacter(63206);
    }

    #[ORM\Column(type: Types::STRING)]
    #[Groups(["social-networks:get"])]
    private ?string $website;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(["social-networks:get"])]
    private ?string $link;

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;

        return $this;
    }
}