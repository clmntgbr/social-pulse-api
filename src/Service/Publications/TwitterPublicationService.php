<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;

readonly class TwitterPublicationService implements PublicationServiceInterface
{
    public function __construct()
    {
    }

    public function create(PostPublications $postPublications): void
    {
    }
}