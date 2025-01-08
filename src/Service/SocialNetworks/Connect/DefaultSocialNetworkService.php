<?php

namespace App\Service\SocialNetworks\Connect;

use App\Dto\Api\GetSocialNetworksCallback;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class DefaultSocialNetworkService implements SocialNetworkServiceInterface
{
    public function __construct(
        private string $frontUrl,
    ) {
    }

    public function getConnectUrl(User $user, string $callbackPath): ?string
    {
        throw new BadRequestHttpException('An error appeared');
    }

    public function create(GetSocialNetworksCallback $getSocialNetworksCallback): RedirectResponse
    {
        return new RedirectResponse(sprintf('%s', $this->frontUrl));
    }
}
