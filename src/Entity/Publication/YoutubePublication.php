<?php

namespace App\Entity\Publication;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\Publication\YoutubePublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: YoutubePublicationRepository::class)]
#[ApiResource(
    operations: []
)]
class YoutubePublication extends Publication
{
    public function __construct()
    {
        parent::__construct();
        $this->setPublicationType(SocialNetworkType::YOUTUBE->toString());
    }
}
