<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;

class DefaultPublicationService extends AbstractPublicationService implements PublicationServiceInterface
{
    public function __construct()
    {
    }

    public function create(PostPublications $postPublications): void
    {
    }

    public function publish(array $publications)
    {
    }

    public function delete(array $publications)
    {
    }
}