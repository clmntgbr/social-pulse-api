<?php

namespace App\Dto\Linkedin;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class LinkedinUploadMedia
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    #[SerializedName('media_id_string')]
    public string $mediaId;

    #[Assert\Type('int')]
    #[Assert\NotBlank()]
    public int $size;

    #[Assert\Type('int')]
    #[Assert\NotBlank()]
    #[SerializedName('expires_after_secs')]
    public int $expiresAfterSecs;
}
