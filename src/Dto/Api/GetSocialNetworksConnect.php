<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

class GetSocialNetworksConnect
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $socialNetworkType = null;

    public ?string $callbackPath = '/';
}