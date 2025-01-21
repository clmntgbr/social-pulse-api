<?php

namespace App\ApiResource;

use App\Dto\Api\GetPublication;
use App\Repository\Publication\PublicationRepository;
use App\Service\Publications\LinkedinPublicationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class DeletePublicationAction extends AbstractController
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository,
        private readonly SerializerInterface $serializer,
        private readonly LinkedinPublicationService $linkedinPublicationService,
    ) {
    }

    public function __invoke(GetPublication $getPublication): JsonResponse
    {
        $publication = $this->publicationRepository->findOneByCriteria([
            'uuid' => $getPublication->uuid,
        ]);

        if (!$publication) {
            return new JsonResponse([
                'message' => 'You dont have access to this publication.'],
                Response::HTTP_FORBIDDEN
            );
        }

        $this->linkedinPublicationService->delete([$publication]);

        return new JsonResponse(
            data: [],
            status: Response::HTTP_OK
        );
    }
}
