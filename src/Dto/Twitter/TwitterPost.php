<?php

namespace App\Dto\Twitter;

use App\Dto\Post;
use Symfony\Component\Validator\Constraints as Assert;

class TwitterPost extends Post
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $id;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $text;
}
