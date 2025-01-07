<?php

namespace App\Entity\Publication;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SocialNetworkType;
use App\Repository\Publication\LinkedinPublicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkedinPublicationRepository::class)]
#[ApiResource(
    operations: []
)]
class LinkedinPublication extends Publication
{
    public function __construct()
    {
        parent::__construct();
        $this->setPublicationType(SocialNetworkType::LINKEDIN->toString());
    }
}
