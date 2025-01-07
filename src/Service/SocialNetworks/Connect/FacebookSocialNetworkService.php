<?php

namespace App\Service\SocialNetworks\Connect;

use App\Dto\AccessToken\FacebookAccessToken;
use App\Dto\Api\GetSocialNetworksCallback;
use App\Dto\SocialNetworksAccount\FacebookAccount;
use App\Dto\SocialNetworksAccount\FacebookData;
use App\Entity\User;
use App\Enum\SocialNetworkType;
use App\Repository\SocialNetwork\FacebookSocialNetworkRepository;
use App\Repository\SocialNetwork\TypeRepository;
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
        private TypeRepository $typeRepository,
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

        $facebookAccounts = $this->facebookApi->getAccounts($accessToken);

        if (!$facebookAccounts instanceof FacebookAccount) {
            return new RedirectResponse(sprintf('%s', $this->frontUrl));
        }

        $socialNetworkType = $this->typeRepository->findOneByCriteria(['name' => SocialNetworkType::FACEBOOK->toString()]);
        $validate = Uuid::uuid4()->toString();

        /** @var FacebookData $facebookAccount */
        foreach ($facebookAccounts->accounts as $facebookAccount) {
            $longAccessToken = $this->facebookApi->getLongAccessToken($facebookAccount->accessToken);

            if (!$longAccessToken instanceof FacebookAccessToken) {
                continue;
            }

            $this->socialNetworkRepository->updateOrCreate([
                'socialNetworkId' => $facebookAccount->id,
                'organization' => $user->getActiveOrganization(),
            ], [
                'socialNetworkId' => $facebookAccount->id,
                'avatarUrl' => $facebookAccount->picture,
                'username' => $facebookAccount->name,
                'name' => $facebookAccount->name,
                'organization' => $user->getActiveOrganization(),
                'token' => $longAccessToken->accessToken,
                'followers' => $facebookAccount->followersCount,
                'followings' => $facebookAccount->fanCount,
                'website' => $facebookAccount->website,
                'link' => $facebookAccount->link,
                'email' => $facebookAccounts->email,
                'validate' => $validate,
                'socialNetworkType' => $socialNetworkType,
            ]);
        }

        return new RedirectResponse(sprintf('%s/%s/social-networks/validate/%s',
            $this->frontUrl,
            $user->getSocialNetworksCallbackPath(),
            $validate
        ));
    }

    public function getScopes(): string
    {
        return 'email,pages_manage_cta,pages_show_list,read_page_mailboxes,business_management,pages_messaging,pages_messaging_subscriptions,page_events,pages_read_engagement,pages_manage_metadata,pages_read_user_content,pages_manage_ads,pages_manage_posts,pages_manage_engagement';
    }
}