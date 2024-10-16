<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TwitterCallback
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $oauth_token;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $oauth_verifier;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $state;
}