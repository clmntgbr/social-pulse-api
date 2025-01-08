<?php

namespace App\Dto\Api;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

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
    public function validateContentOrPictures(\Symfony\Component\Validator\Context\ExecutionContextInterface $context): void
    {
        if ((!isset($this->content) || (null === $this->content || '' === $this->content || '0' === $this->content)) && [] === $this->pictures) {
            $context->buildViolation('Either content or pictures must be provided, but not both missing.')
                ->atPath('content')
                ->addViolation();
        }
    }
}
