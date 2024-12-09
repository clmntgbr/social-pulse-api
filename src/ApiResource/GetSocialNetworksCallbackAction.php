<?php

namespace App\ApiResource;

use App\Dto\Api\GetSocialNetworksCallback;
use App\Service\SocialNetworks\Connect\SocialNetworkServiceFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class GetSocialNetworksCallbackAction extends AbstractController
{
    public function __construct(
        private readonly SocialNetworkServiceFactory $socialNetworkServiceFactory,
        private readonly SerializerInterface $serializer
    ) {}

    public function __invoke(GetSocialNetworksCallback $getSocialNetworksCallback): JsonResponse
    {
        $service = $this->socialNetworkServiceFactory->getService($getSocialNetworksCallback->socialNetworkType);
        $service->create($getSocialNetworksCallback);

        return new JsonResponse(
            data: $this->serializer->serialize([], 'json'),
            status: Response::HTTP_OK,
            json: true
        );
    }
}