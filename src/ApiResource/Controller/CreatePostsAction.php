<?php

namespace App\ApiResource\Controller;

use App\Dto\CreatePostsResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WorkspaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class CreatePostsAction extends AbstractController
{
    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly UserRepository $userRepository,
        private readonly SerializerInterface $serializer
    ) {}

    public function __invoke(CreatePostsResponse $createPostsResponse, #[CurrentUser] User $user): JsonResponse
    {
        dd($createPostsResponse);
    }
}