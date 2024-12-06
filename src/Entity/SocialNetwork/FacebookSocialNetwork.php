<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\SocialNetwork\FacebookSocialNetworkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacebookSocialNetworkRepository::class)]
#[ApiResource(
    operations: []
)]
class FacebookSocialNetwork extends SocialNetwork
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialNetworkType(SocialNetworkType::FACEBOOK->toString());
    }
}
