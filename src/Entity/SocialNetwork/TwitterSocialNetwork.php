<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SocialNetwork\TwitterSocialNetworkRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TwitterSocialNetworkRepository::class)]
#[ApiResource(
    operations: []
)]
class TwitterSocialNetwork extends SocialNetwork
{
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $tokenSecret = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $bearerToken = null;

    public function __construct()
    {
        parent::__construct();
        $this->setMaxCharacter(280);
    }

    public function getBearerToken(): ?string
    {
        return $this->bearerToken;
    }

    public function setBearerToken(?string $bearerToken): static
    {
        $this->bearerToken = $bearerToken;

        return $this;
    }

    public function getTokenSecret(): ?string
    {
        return $this->tokenSecret;
    }

    public function setTokenSecret(?string $tokenSecret): static
    {
        $this->tokenSecret = $tokenSecret;

        return $this;
    }
}
