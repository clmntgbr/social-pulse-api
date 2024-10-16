<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TwitterOAuthToken
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $oauth_token;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $oauth_token_secret;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $oauth_callback_confirmed;
}