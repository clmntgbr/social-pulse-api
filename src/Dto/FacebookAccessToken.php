<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class FacebookAccessToken extends AbstractAccessToken
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $access_token;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $token_type;
}