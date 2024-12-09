<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

class PostOrganizations
{
    #[Assert\Type('string')]
    public ?string $name;

    #[Assert\Type('string')]
    public ?string $logo;
}