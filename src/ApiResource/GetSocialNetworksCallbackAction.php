<?php

namespace App\ApiResource;

use App\Dto\Api\GetSocialNetworksCallback;
use App\Service\SocialNetworks\Connect\SocialNetworkServiceFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetSocialNetworksCallbackAction extends AbstractController
{
    public function __construct(
        private readonly SocialNetworkServiceFactory $socialNetworkServiceFactory
    ) {}

    public function __invoke(GetSocialNetworksCallback $getSocialNetworksCallback): RedirectResponse
    {
        $service = $this->socialNetworkServiceFactory->getService($getSocialNetworksCallback->socialNetworkType);
        return $service->create($getSocialNetworksCallback);
    }
}