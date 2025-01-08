<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;

readonly class DefaultPublicationService implements PublicationServiceInterface
{
    public function __construct()
    {
    }

    public function create(PostPublications $postPublications): void
    {
    }
}
