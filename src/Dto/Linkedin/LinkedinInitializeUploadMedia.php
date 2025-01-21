<?php

namespace App\Dto\Linkedin;

use Symfony\Component\Serializer\Attribute\SerializedPath;
use Symfony\Component\Validator\Constraints as Assert;

class LinkedinInitializeUploadMedia
{
    #[Assert\Type('int')]
    #[Assert\NotBlank()]
    #[SerializedPath('[value][uploadUrlExpiresAt]')]
    public int $uploadUrlExpiresAt;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    #[SerializedPath('[value][uploadUrl]')]
    public string $uploadUrl;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    #[SerializedPath('[value][image]')]
    public string $image;
}
