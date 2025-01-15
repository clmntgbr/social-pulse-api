<?php

namespace App\Service\SocialNetworks\Connect;

use App\Dto\AccessToken\LinkedinAccessToken;
use App\Dto\Api\GetSocialNetworksCallback;
use App\Dto\SocialNetworksAccount\LinkedinAccount;
use App\Entity\User;
use App\Enum\SocialNetworkType;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use App\Repository\SocialNetwork\TypeRepository;
use App\Repository\UserRepository;
use App\Service\LinkedinApi;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class LinkedinSocialNetworkService implements SocialNetworkServiceInterface
{
    public function __construct(
        private LinkedinApi $linkedinApi,
        private UserRepository $userRepository,
        private LinkedinSocialNetworkRepository $linkedinSocialNetworkRepository,
        private TypeRepository $typeRepository,
        private string $linkedinLoginUrl,
        private string $linkedinClientId,
        private string $linkedinCallbackUrl,
        private string $frontUrl,
    ) {
    }

    public function getConnectUrl(User $user, string $callbackPath): string
    {
        /** @var User $user */
        $user = $this->userRepository->update($user, [
            'socialNetworksState' => Uuid::uuid4()->toString(),
            'socialNetworksCallbackPath' => $callbackPath,
        ]);

        return sprintf('%s/oauth/v2/authorization?response_type=code&client_id=%s&redirect_uri=%s&state=%s&scope=%s',
            $this->linkedinLoginUrl,
            $this->linkedinClientId,
            sprintf($this->linkedinCallbackUrl, 'linkedin'),
            $user->getSocialNetworksState(),
            $this->getScopes()
        );
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function create(GetSocialNetworksCallback $getSocialNetworksCallback): RedirectResponse
    {
        /** @var ?User $user */
        $user = $this->userRepository->findOneByCriteria(['socialNetworksState' => $getSocialNetworksCallback->state]);

        if (!$user) {
            return new RedirectResponse(sprintf('%s', $this->frontUrl));
        }

        $accessToken = $this->linkedinApi->getAccessToken($getSocialNetworksCallback->code);

        if (!$accessToken instanceof LinkedinAccessToken) {
            return new RedirectResponse(sprintf('%s', $this->frontUrl));
        }

        $linkedinAccount = $this->linkedinApi->getAccounts($accessToken);

        if (!$linkedinAccount instanceof LinkedinAccount) {
            return new RedirectResponse(sprintf('%s', $this->frontUrl));
        }

        $socialNetworkType = $this->typeRepository->findOneByCriteria(['name' => SocialNetworkType::LINKEDIN->toString()]);
        $validate = Uuid::uuid4()->toString();

        $this->linkedinSocialNetworkRepository->updateOrCreate([
            'socialNetworkId' => $linkedinAccount->sub,
            'organization' => $user->getActiveOrganization(),
        ], [
            'socialNetworkId' => $linkedinAccount->sub,
            'avatarUrl' => $linkedinAccount->picture,
            'username' => $linkedinAccount->name,
            'name' => sprintf('%s %s', $linkedinAccount->givenName, $linkedinAccount->familyName),
            'organization' => $user->getActiveOrganization(),
            'token' => $accessToken->accessToken,
            'isVerified' => $linkedinAccount->emailVerified,
            'country' => $linkedinAccount->locale['country'] ?? null,
            'language' => $linkedinAccount->locale['language'] ?? null,
            'email' => $linkedinAccount->email,
            'validate' => $validate,
            'socialNetworkType' => $socialNetworkType,
        ]);

        return new RedirectResponse(sprintf('%s/%s/social-networks/validate/%s',
            $this->frontUrl,
            $user->getSocialNetworksCallbackPath(),
            $validate
        ));
    }

    public function getScopes(): string
    {
        return 'profile,email,openid,w_member_social';
    }
}