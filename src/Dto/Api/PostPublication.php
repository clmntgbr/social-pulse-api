<?php

namespace App\Dto\Api;

use App\Entity\SocialNetwork\SocialNetwork;
use App\Enum\PublicationStatus;
use App\Enum\PublicationThreadType;
use App\Enum\SocialNetworkType;
use DateTimeInterface;
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

    #[Assert\Choice(choices: ['programmed', 'draft'],)]
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
        if (empty($this->content) && empty($this->pictures)) {
            $context->buildViolation('Either content or pictures must be provided, but not both missing.')
                ->atPath('content')
                ->addViolation();
        }
    }
}