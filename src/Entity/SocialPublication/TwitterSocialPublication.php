<?php

namespace App\Entity\SocialPublication;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\SocialPublication\TwitterSocialPublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TwitterSocialPublicationRepository::class)]
#[ApiResource(
    operations: []
)]
class TwitterSocialPublication extends SocialPublication
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialPublicationType(SocialNetworkType::TWITTER->toString());
    }
}
