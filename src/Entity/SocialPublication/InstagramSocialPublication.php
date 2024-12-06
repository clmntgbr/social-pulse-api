<?php

namespace App\Entity\SocialPublication;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\SocialPublication\InstagramSocialPublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstagramSocialPublicationRepository::class)]
#[ApiResource(
    operations: []
)]
class InstagramSocialPublication extends SocialPublication
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialPublicationType(SocialNetworkType::INSTAGRAM->toString());
    }
}