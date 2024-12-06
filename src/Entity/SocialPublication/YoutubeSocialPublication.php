<?php

namespace App\Entity\SocialPublication;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\SocialPublication\YoutubeSocialPublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: YoutubeSocialPublicationRepository::class)]
#[ApiResource(
    operations: []
)]
class YoutubeSocialPublication extends SocialPublication
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialPublicationType(SocialNetworkType::YOUTUBE->toString());
    }
}
