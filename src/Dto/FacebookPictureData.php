<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class FacebookPictureData
{
    #[Assert\Url()]
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $url;
}
