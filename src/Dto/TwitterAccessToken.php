<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TwitterAccessToken extends AbstractAccessToken
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $oauth_token;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $oauth_token_secret;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $user_id;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $screen_name;
}