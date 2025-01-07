<?php

namespace App\Entity\Publication;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\Publication\FacebookPublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacebookPublicationRepository::class)]
#[ApiResource(
    operations: []
)]
class FacebookPublication extends Publication
{
    public function __construct()
    {
        parent::__construct();
        $this->setPublicationType(SocialNetworkType::FACEBOOK->toString());
    }
}
