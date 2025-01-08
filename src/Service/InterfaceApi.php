<?php

namespace App\Service;

use App\Dto\AccessToken\AbstractAccessToken;

interface InterfaceApi
{
    public function getAccessToken(string ...$params): ?AbstractAccessToken;
}
