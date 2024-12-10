<?php

namespace App\Service\SocialNetworks\Connect;

use App\Dto\AccessToken\FacebookAccessToken;
use App\Dto\Api\GetSocialNetworksCallback;
use App\Dto\SocialNetworksAccount\FacebookAccount;
use App\Dto\SocialNetworksAccount\FacebookData;
use App\Entity\User;
use App\Repository\SocialNetwork\FacebookSocialNetworkRepository;
use App\Repository\UserRepository;
use App\Service\FacebookApi;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class FacebookSocialNetworkService implements SocialNetworkServiceInterface
{
    public function __construct(
        private FacebookApi $facebookApi,
        private UserRepository $userRepository,
        private FacebookSocialNetworkRepository $socialNetworkRepository,
        private string $facebookLoginUrl,
        private string $facebookClientId,
        private string $facebookCallbackUrl,
        private string $frontUrl
    )
    {
    }

    public function getConnectUrl(User $user, string $callbackPath): string
    {
        /** @var User $user */
        $user = $this->userRepository->update($user, [
            'socialNetworksState' => Uuid::uuid4()->toString(),
            'socialNetworksCallbackPath' => $callbackPath
        ]);

        return sprintf('%s/dialog/oauth?client_id=%s&redirect_uri=%s&scope=%s&state=%s',
            $this->facebookLoginUrl,
            $this->facebookClientId,
            sprintf($this->facebookCallbackUrl, 'facebook'),
            $this->getScopes(),
            $user->getSocialNetworksState()
        );
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function create(GetSocialNetworksCallback $getSocialNetworksCallback): RedirectResponse
    {
        /** @var ?User $user */
        $user = $this->userRepository->findOneByCriteria(['socialNetworksState' => $getSocialNetworksCallback->state]);

        if (!$user) {
            return new RedirectResponse(sprintf('%s', $this->frontUrl));
        }

        $accessToken = $this->facebookApi->getAccessToken($getSocialNetworksCallback->code);

        if (!$accessToken instanceof FacebookAccessToken) {
            return new RedirectResponse(sprintf('%s', $this->frontUrl));
        }

        $accounts = $this->facebookApi->getAccounts($accessToken);

        if (!$accounts instanceof FacebookAccount) {
            return new RedirectResponse(sprintf('%s', $this->frontUrl));
        }

        /** @var FacebookData $account */
        foreach ($accounts->accounts as $account) {
            $longAccessToken = $this->facebookApi->getLongAccessToken($account->accessToken);

            if (!$longAccessToken instanceof FacebookAccessToken) {
                continue;
            }

            $this->socialNetworkRepository->updateOrCreate([
                'socialNetworkId' => $account->id,
                'organization' => $user->getActiveOrganization(),
            ], [
                'socialNetworkId' => $account->id,
                'avatarUrl' => $account->picture,
                'username' => $account->name,
                'name' => $account->name,
                'organization' => $user->getActiveOrganization(),
                'token' => $longAccessToken->accessToken,
                'followers' => $account->followersCount,
                'followings' => $account->fanCount,
                'website' => $account->website,
                'link' => $account->link,
                'email' => $accounts->email,
            ]);
        }

        return new RedirectResponse(sprintf('%s%s', $this->frontUrl, $user->getSocialNetworksCallbackPath()));
    }

    public function getScopes(): string
    {
        return 'email,pages_manage_cta,pages_show_list,read_page_mailboxes,business_management,pages_messaging,pages_messaging_subscriptions,page_events,pages_read_engagement,pages_manage_metadata,pages_read_user_content,pages_manage_ads,pages_manage_posts,pages_manage_engagement';
    }
}