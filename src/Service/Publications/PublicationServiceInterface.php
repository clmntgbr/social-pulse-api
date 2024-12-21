<?php

namespace App\Service\Publications;

use App\Dto\Api\GetSocialNetworksCallback;
use App\Dto\Api\PostPublications;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

interface PublicationServiceInterface
{
    public function create(PostPublications $postPublications): void;
}