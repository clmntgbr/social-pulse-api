<?php

namespace App\Dto\Api;

use App\Enum\SocialNetworkType;
use Symfony\Component\Validator\Constraints as Assert;

class PostPublications
{
    #[Assert\NotBlank()]
    #[Assert\Choice(choices: ['facebook', 'twitter', 'linkedin', 'youtube', 'instagram'])]
    #[Assert\Type('string')]
    public string $publicationType;

    #[Assert\NotBlank()]
    public string $socialNetworkUuid;

    #[Assert\Count(min: 1)]
    #[Assert\Valid()]
    /** @var PostPublication[] $publications */
    public array $publications = [];

    public function __construct($publications)
    {
        $this->publications = $publications;
    }
}