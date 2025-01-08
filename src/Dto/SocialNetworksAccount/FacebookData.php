<?php

namespace App\Dto\SocialNetworksAccount;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class FacebookData extends AbstractAccount
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $name;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    #[SerializedName('access_token')]
    public string $accessToken;

    public int $followersCount = 0;

    public int $fanCount = 0;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $id;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $link;

    public ?string $website;

    public ?string $picture;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    #[SerializedName('page_token')]
    public string $pageToken;
}
