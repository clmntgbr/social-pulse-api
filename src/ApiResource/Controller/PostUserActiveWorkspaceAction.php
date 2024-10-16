<?php

namespace App\ApiResource\Controller;

use App\Dto\UserActiveWorkspace;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WorkspaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class PostUserActiveWorkspaceAction extends AbstractController
{
    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly UserRepository $userRepository,
        private readonly SerializerInterface $serializer
    ) {}

    public function __invoke(UserActiveWorkspace $userActiveWorkspace, #[CurrentUser] User $user): JsonResponse
    {
        $workspaces = $user->getWorkspaces();

        $isOwner = $workspaces->exists(function ($key, $workspace) use ($userActiveWorkspace) {
            return $workspace->getUuid() === $userActiveWorkspace->workspaceUuid;
        });

        if (!$isOwner) {
            return new JsonResponse([
                    'message' => 'You dont have access to this workspace.',
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        $workspace = $this->workspaceRepository->findOneBy(['uuid' => $userActiveWorkspace->workspaceUuid]);

        $this->userRepository->update($user, [
           'activeWorkspace' => $workspace
        ]);

        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups('get_workspaces')
            ->toArray();

        return new JsonResponse(
            data: $this->serializer->serialize($workspace, 'jsonld', $context),
            status: Response::HTTP_OK,
            json: true
        );
    }
}