<?php

namespace App\Entity\Publication;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\Publication\TwitterPublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TwitterPublicationRepository::class)]
#[ApiResource(
    operations: []
)]
class TwitterPublication extends Publication
{
    public function __construct()
    {
        parent::__construct();
        $this->setPublicationType(SocialNetworkType::TWITTER->toString());
    }
}
