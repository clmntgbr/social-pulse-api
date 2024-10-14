<?php

namespace App\Service;

use App\Entity\User;

class TwitterLoginUrl implements InterfaceLoginUrl
{
    public function __construct(
        private readonly string $twitterCLientId,
        private readonly string $twitterClientSecret,
        private readonly string $twitterCallbackUrl,
        private readonly string $twitterApiUrl,
        private readonly string $twitterLoginUrl,
        private readonly string $twitterApiKey,
        private readonly string $twitterApiSecret
    ) {}

    public function getLoginUrl(User $user, string $callback): string
    {
        return  '';
    }

    public function getScopes(): string
    {
        return  '[]';
    }
}