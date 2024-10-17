<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LinkedinAccount
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $sub;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $name;

    #[Assert\Type('array')]
    public array $locale;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $email;

    public ?string $given_name;
    public ?string $family_name;
    public ?string $picture;
    public ?bool $email_verified;
}
