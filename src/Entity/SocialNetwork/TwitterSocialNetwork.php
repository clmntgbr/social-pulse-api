<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\SocialNetwork\TwitterSocialNetworkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TwitterSocialNetworkRepository::class)]
#[ApiResource(
    operations: []
)]
class TwitterSocialNetwork extends SocialNetwork
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialNetworkType(SocialNetworkType::TWITTER->toString());
    }
}
