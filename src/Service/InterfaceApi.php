<?php

namespace App\Service;

use App\Dto\AccessToken\AbstractAccessToken;
use App\Dto\Post;
use App\Entity\SocialNetwork\SocialNetwork;

interface InterfaceApi
{
    public function getAccessToken(string ...$params): ?AbstractAccessToken;
    public function post(SocialNetwork $socialNetwork, array $payload): Post;
}