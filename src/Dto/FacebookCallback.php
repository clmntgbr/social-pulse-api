<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class FacebookCallback
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $code;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $state;
}