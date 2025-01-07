<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: LinkedinSocialNetworkRepository::class)]
#[ApiResource(
    operations: []
)]
class LinkedinSocialNetwork extends SocialNetwork
{
    public function __construct()
    {
        parent::__construct();
        $this->setMaxCharacter(3000);
    }

    #[ORM\Column(type: Types::STRING)]
    #[Groups(["social-networks:get"])]
    private ?string $country;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(["social-networks:get"])]
    private ?string $language;

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }
}