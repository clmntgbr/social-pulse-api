<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\SocialNetwork\InstagramSocialNetworkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstagramSocialNetworkRepository::class)]
#[ApiResource(
    operations: []
)]
class InstagramSocialNetwork extends SocialNetwork
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialNetworkType(SocialNetworkType::INSTAGRAM->toString());
    }
}