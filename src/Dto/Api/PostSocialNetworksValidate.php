<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

class PostSocialNetworksValidate
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $validate = null;

    #[Assert\Type('array')]
    public array $uuids = [];
}