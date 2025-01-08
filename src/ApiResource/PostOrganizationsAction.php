<?php

namespace App\ApiResource;

use App\Dto\Api\PostOrganizations;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Service\ImageService;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class PostOrganizationsAction extends AbstractController
{
    public function __construct(
        private readonly ImageService $imageService,
        private readonly OrganizationRepository $organizationRepository,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(PostOrganizations $postOrganizations, #[CurrentUser] ?User $user): JsonResponse
    {
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups(['organization:get', 'default'])
            ->toArray();

        $uuid = Uuid::uuid4()->toString();
        $logoUrl = $this->imageService->saveBase64File('organizations', $uuid, $postOrganizations->logo);

        $this->organizationRepository->create([
            'logoUrl' => $logoUrl,
            'uuid' => $uuid,
            'name' => $postOrganizations->name,
            'admin' => $user,
            'user' => $user,
        ]);

        return new JsonResponse(
            data: $this->serializer->serialize($user->getOrganizations(), 'json', $context),
            status: Response::HTTP_CREATED,
            json: true
        );
    }
}
