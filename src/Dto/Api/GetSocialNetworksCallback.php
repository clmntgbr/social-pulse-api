<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

class GetSocialNetworksCallback
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $code = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $state = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $socialNetworkType = null;
}