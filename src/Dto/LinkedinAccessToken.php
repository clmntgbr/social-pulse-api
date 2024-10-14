<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LinkedinAccessToken extends AbstractAccessToken
{
    #[Assert\NotBlank()]
    #[Assert\Type('int')]
    public ?int $expires_in;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $scope;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $id_token;
}