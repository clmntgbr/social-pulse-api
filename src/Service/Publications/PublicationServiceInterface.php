<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;

interface PublicationServiceInterface
{
    public function create(PostPublications $postPublications): void;
    public function publish(array $publications);
    public function delete(array $publications);
}
