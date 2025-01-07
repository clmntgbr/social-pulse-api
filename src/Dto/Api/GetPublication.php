<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

class GetPublication
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $uuid = null;
}