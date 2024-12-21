<?php

namespace App\Service\Publications;

use App\Dto\Api\GetSocialNetworksCallback;
use App\Dto\Api\PostPublications;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class DefaultPublicationService implements PublicationServiceInterface
{
    public function __construct() {}

    public function create(PostPublications $postPublications): void
    {
    }
}