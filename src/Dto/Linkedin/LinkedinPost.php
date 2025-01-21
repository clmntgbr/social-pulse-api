<?php

namespace App\Dto\Linkedin;

use App\Dto\Post;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class LinkedinPost extends Post
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    #[SerializedName('x-restli-id')]
    public string $id;

    public function setId(array $headers): void
    {
        if (isset($headers[0])) {
            $this->id = str_replace('urn:li:share:', '', $headers[0]);
        }
    }
}
