<?php

namespace App\ApiResource;

use App\Dto\Api\GetSocialNetworksConnect;
use App\Entity\User;
use App\Service\SocialNetworks\Connect\SocialNetworkServiceFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class GetSocialNetworksConnectAction extends AbstractController
{
    public function __construct(
        private readonly SocialNetworkServiceFactory $socialNetworkServiceFactory,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(GetSocialNetworksConnect $getSocialNetworksConnect, #[CurrentUser] ?User $user): JsonResponse
    {
        $socialNetworkService = $this->socialNetworkServiceFactory->getService($getSocialNetworksConnect->socialNetworkType);
        $url = $socialNetworkService->getConnectUrl($user, $getSocialNetworksConnect->callbackPath);

        return new JsonResponse(
            data: $this->serializer->serialize(['url' => $url], 'json'),
            status: Response::HTTP_OK,
            json: true
        );
    }
}
