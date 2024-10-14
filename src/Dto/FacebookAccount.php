<?php

// src/DTO/FacebookAccountDTO.php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class FacebookAccount
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $name;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $access_token;

    #[Assert\Type('array')]
    #[Assert\All([new Assert\Email()])]
    public array $emails;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $id;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $page_token;

    #[Assert\Valid()]
    #[Assert\NotBlank()]
    public FacebookPicture $picture;

    public ?string $website = null;
    public ?string $link = null;
}
