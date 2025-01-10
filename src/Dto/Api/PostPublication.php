<?php

namespace App\Dto\Api;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PostPublication
{
    #[Assert\NotBlank()]
    #[Assert\Choice(choices: ['facebook', 'twitter', 'linkedin', 'youtube', 'instagram'])]
    #[Assert\Type('string')]
    public ?string $publicationType;

    #[Assert\Type('string')]
    public ?string $content;

    #[Assert\Type('int')]
    public ?int $id;

    #[Assert\Choice(choices: ['scheduled', 'draft'], )]
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $status;

    #[Assert\Choice(choices: ['primary', 'secondary'])]
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $threadType;

    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Type('datetime')]
    public \DateTime $publishedAt;

    #[Assert\Type('array')]
    public array $pictures = [];

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[SerializedName('socialNetwork')]
    public string $socialNetworkUuid;

    public function setSocialNetworkUuid(array $socialNetwork): void
    {
        $this->socialNetworkUuid = $socialNetwork['uuid'];
    }

    #[Assert\Callback]
    public function validateContentOrPictures(ExecutionContextInterface $executionContext): void
    {
        if (empty($this->content) && empty($this->pictures)) {
            $executionContext->buildViolation('Either content or pictures must be provided, but not both missing.')
                ->atPath('content')
                ->addViolation();
        }
    }
}