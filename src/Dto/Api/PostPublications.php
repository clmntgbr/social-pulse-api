<?php

namespace App\Dto\Api;

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
    /** @var PostPublication[] */
    public array $publications = [];

    public function __construct(array $publications)
    {
        $this->publications = $publications;
    }
}
