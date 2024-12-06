<?php

namespace App\Entity\SocialPublication;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\SocialPublication\LinkedinSocialPublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkedinSocialPublicationRepository::class)]
#[ApiResource(
    operations: []
)]
class LinkedinSocialPublication extends SocialPublication
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialPublicationType(SocialNetworkType::LINKEDIN->toString());
    }
}
