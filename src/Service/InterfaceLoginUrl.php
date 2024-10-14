<?php

namespace App\Service;

use App\Entity\User;

interface InterfaceLoginUrl
{
    public function getLoginUrl(User $user, string $callback): string;
    public function getScopes(): string;
}