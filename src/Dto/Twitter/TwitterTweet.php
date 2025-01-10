<?php

namespace App\Dto\Twitter;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class TwitterTweet
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $id;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $text;
}