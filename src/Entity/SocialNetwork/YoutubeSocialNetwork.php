<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\SocialNetwork\YoutubeSocialNetworkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: YoutubeSocialNetworkRepository::class)]
#[ApiResource(
    operations: []
)]
class YoutubeSocialNetwork extends SocialNetwork
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialNetworkType(SocialNetworkType::YOUTUBE->toString());
    }
}