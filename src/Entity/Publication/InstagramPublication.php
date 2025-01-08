<?php

namespace App\Entity\Publication;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\Publication\InstagramPublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstagramPublicationRepository::class)]
#[ApiResource(
    operations: []
)]
class InstagramPublication extends Publication
{
    public function __construct()
    {
        parent::__construct();
        $this->setPublicationType(SocialNetworkType::INSTAGRAM->toString());
    }
}
