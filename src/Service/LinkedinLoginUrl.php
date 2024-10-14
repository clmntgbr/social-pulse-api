<?php

namespace App\Service;

use App\Entity\User;

class LinkedinLoginUrl implements InterfaceLoginUrl
{
    public function __construct(
        private readonly string $linkedinClientId,
        private readonly string $linkedinClientSecret,
        private readonly string $linkedinCallbackUrl,
        private readonly string $linkedinApiUrl,
        private readonly string $linkedinLoginUrl
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