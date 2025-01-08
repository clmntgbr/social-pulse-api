<?php

namespace App\ApiResource;

use App\Dto\Api\PostPublications;
use App\Entity\User;
use App\Repository\Publication\PublicationRepository;
use App\Service\Publications\PublicationServiceFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class PostPublicationsAction extends AbstractController
{
    public function __construct(
        private readonly PublicationServiceFactory $publicationServiceFactory,
        private readonly PublicationRepository $publicationRepository,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(PostPublications $postPublications, Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups(['publications:get', 'social-networks:get', 'social-networks-type:get', 'default'])
            ->toArray();

        $service = $this->publicationServiceFactory->getService($postPublications->publicationType);
        $service->create($postPublications);

        return new JsonResponse(
            data: $this->serializer->serialize($this->publicationRepository->findAll(), 'json', $context),
            status: Response::HTTP_CREATED,
            json: true
        );
    }
}
