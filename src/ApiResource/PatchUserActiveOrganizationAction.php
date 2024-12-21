<?php

namespace App\ApiResource;

use App\Dto\Api\PatchUserActiveOrganization;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class PatchUserActiveOrganizationAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly OrganizationRepository $organizationRepository,
        private readonly SerializerInterface $serializer
    ) {}

    public function __invoke(PatchUserActiveOrganization $patchUserActiveOrganization, #[CurrentUser] ?User $user): JsonResponse
    {
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups(['user:get'])
            ->toArray();

        if (!$user->isOneOfMine($patchUserActiveOrganization->organizationUuid)) {
            return new JsonResponse(['message' => 'You dont have access to this workspace.'],
                Response::HTTP_FORBIDDEN
            );
        }

        $organization = $this->organizationRepository->findOneByCriteria(['uuid' => $patchUserActiveOrganization->organizationUuid]);

        $this->userRepository->update($user, [
            'activeOrganization' => $organization
        ]);

        return new JsonResponse(
            data: $this->serializer->serialize($user, 'json', $context),
            status: Response::HTTP_OK,
            json: true
        );
    }
}