<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TwitterBearerToken extends AbstractAccessToken
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $token_type;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $access_token;
}