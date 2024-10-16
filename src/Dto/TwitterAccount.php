<?php

// src/DTO/FacebookAccountDTO.php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TwitterAccount
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $username;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $name;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $id;

    public bool $verified;
    public ?string $profile_image_url;
}
