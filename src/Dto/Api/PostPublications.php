<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

class PostPublications
{
    #[Assert\NotBlank()]
    public string $publicationType;

    #[Assert\NotBlank()]
    public string $socialNetworkUuid;

    #[Assert\Count(min: 1)]
    /** @var PostPublication[] $publications */
    public array $publications = [];

    public function __construct($publications)
    {
        $this->publications = $publications;
    }
}