<?php

namespace App\Service\SocialNetworks\Connect;

use App\Dto\Api\GetSocialNetworksCallback;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FacebookApi;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class FacebookSocialNetworkService implements SocialNetworkServiceInterface
{
    public function __construct(
        private readonly FacebookApi $facebookApi,
        private UserRepository $userRepository,
        private string         $facebookLoginUrl,
        private string         $facebookClientId,
        private string         $facebookCallbackUrl
    ) {}

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
    public function create(GetSocialNetworksCallback $getSocialNetworksCallback): void
    {
        $user = $this->userRepository->findOneByCriteria(['socialNetworksState' => $getSocialNetworksCallback->state]);

        if (!$user) {
            throw new JsonException('User not found', Response::HTTP_NOT_FOUND);
        }

        $accessToken = $this->facebookApi->getAccessToken($getSocialNetworksCallback->code);
        $accounts = $this->facebookApi->getAccounts($accessToken);

        dd($accounts);
    }

    public function getScopes(): string
    {
        return 'email,pages_manage_cta,pages_show_list,read_page_mailboxes,business_management,pages_messaging,pages_messaging_subscriptions,page_events,pages_read_engagement,pages_manage_metadata,pages_read_user_content,pages_manage_ads,pages_manage_posts,pages_manage_engagement';
    }
}