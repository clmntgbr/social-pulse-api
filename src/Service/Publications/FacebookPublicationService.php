<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;

readonly class FacebookPublicationService implements PublicationServiceInterface
{
    public function __construct()
    {
    }

    public function create(PostPublications $postPublications): void
    {
    }
}