<?php

namespace App\ApiResource;

use App\Dto\Api\GetPublication;
use App\Entity\User;
use App\Repository\Publication\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class GetPublicationAction extends AbstractController
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(GetPublication $getPublication): JsonResponse
    {
        $publications = $this->publicationRepository->findPublicationByThreadUuid($getPublication->uuid);

        if (count($publications) <= 0) {
            return new JsonResponse([
                'message' => 'You dont have access to this publication.'],
                Response::HTTP_FORBIDDEN
            );
        }

        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups(['publications:get', 'publication:get', 'social-networks:get', 'social-networks-type:get', 'default'])
            ->toArray();

        return new JsonResponse(
            data: $this->serializer->serialize($publications, 'json', $context),
            status: Response::HTTP_OK,
            json: true
        );
    }
}