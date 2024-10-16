<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\FacebookSocialAccountRepository;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;

readonly class FacebookLoginUrl implements InterfaceLoginUrl
{
    public function __construct(
        private FacebookSocialAccountRepository $facebookSocialAccountRepository,
        private UserRepository $userRepository,
        private string $facebookClientId,
        private string $facebookCallbackUrl,
        private string $facebookLoginUrl
    ) {}

    public function getLoginUrl(User $user, string $callback): string
    {
        $user = $this->userRepository->update($user, [
            'state' => Uuid::uuid4()->toString(),
            'callback' => $callback
        ]);

        $this->facebookSocialAccountRepository->create([
            'workspace' => $user->getActiveWorkspace(),
            'token' => Uuid::uuid4()->toString(),
            'socialAccountId' => $user->getState(),
            'scopes' => $this->getScopes(),
        ]);

        return sprintf('%s/dialog/oauth?client_id=%s&redirect_uri=%s&scope=%s&state=%s', $this->facebookLoginUrl, $this->facebookClientId, $this->facebookCallbackUrl, $this->getScopes(), $user->getState());
    }

    public function getScopes(): string
    {
        return 'email,pages_manage_cta,pages_show_list,read_page_mailboxes,business_management,pages_messaging,pages_messaging_subscriptions,page_events,pages_read_engagement,pages_manage_metadata,pages_read_user_content,pages_manage_ads,pages_manage_posts,pages_manage_engagement';
    }
}