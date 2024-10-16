<?php

namespace App\Service;

use App\Dto\AbstractAccessToken;

interface InterfaceApi
{
    public function getAccessToken(string ...$params): AbstractAccessToken;
}