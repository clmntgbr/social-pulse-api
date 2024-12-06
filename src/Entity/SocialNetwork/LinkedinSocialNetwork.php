<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkedinSocialNetworkRepository::class)]
#[ApiResource(
    operations: []
)]
class LinkedinSocialNetwork extends SocialNetwork
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialNetworkType(SocialNetworkType::LINKEDIN->toString());
    }
}
