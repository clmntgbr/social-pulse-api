<?php

namespace App\Entity\SocialPublication;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\SocialPublication\FacebookSocialPublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacebookSocialPublicationRepository::class)]
#[ApiResource(
    operations: []
)]
class FacebookSocialPublication extends SocialPublication
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialPublicationType(SocialNetworkType::FACEBOOK->toString());
    }
}
