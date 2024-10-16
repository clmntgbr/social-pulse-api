<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\LinkedinSocialAccountRepository;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;

class LinkedinLoginUrl implements InterfaceLoginUrl
{
    public function __construct(
        private readonly string                          $linkedinClientId,
        private readonly string                          $linkedinCallbackUrl,
        private readonly string                          $linkedinLoginUrl,
        private readonly UserRepository                  $userRepository,
        private readonly LinkedinSocialAccountRepository $linkedinSocialAccountRepository
    ) {}

    public function getLoginUrl(User $user, string $callback): string
    {
        $user = $this->userRepository->update($user, [
            'state' => Uuid::uuid4()->toString(),
            'callback' => $callback
        ]);

        $this->linkedinSocialAccountRepository->create([
            'token' => Uuid::uuid4()->toString(),
            'workspace' => $user->getActiveWorkspace(),
            'socialAccountId' => $user->getState(),
            'scopes' => $this->getScopes(),
        ]);

        return sprintf('%s/oauth/v2/authorization?response_type=code&client_id=%s&redirect_uri=%s&state=%s&scope=%s', $this->linkedinLoginUrl, $this->linkedinClientId, $this->linkedinCallbackUrl, $user->getState(), $this->getScopes());

    }

    public function getScopes(): string
    {
        return 'profile,email,openid,w_member_social';
    }
}