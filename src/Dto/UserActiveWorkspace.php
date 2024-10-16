<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserActiveWorkspace
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $workspaceUuid;
}