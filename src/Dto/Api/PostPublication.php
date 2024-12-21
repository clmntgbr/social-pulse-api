<?php

namespace App\Dto\Api;

use App\Entity\SocialNetwork\SocialNetwork;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class PostPublication
{
    #[Assert\Type('string')]
    public ?string $publicationType;

    #[Assert\Type('string')]
    public ?string $content;

    #[Assert\Type('string')]
    public ?string $status;

    #[Assert\Type('string')]
    public ?string $threadType;

    #[Assert\Type('datetime')]
    public \DateTime $publishedAt;

    #[Assert\Type('array')]
    public array $pictures;

    #[Assert\Type('string')]
    #[SerializedName('socialNetwork')]
    public string $socialNetworkUuid;

    public function setSocialNetworkUuid(array $socialNetwork): void
    {
        $this->socialNetworkUuid = $socialNetwork['uuid'];
    }
}