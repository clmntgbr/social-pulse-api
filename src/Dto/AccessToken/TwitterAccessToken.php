<?php

namespace App\Dto\AccessToken;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class TwitterAccessToken extends AbstractAccessToken
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[SerializedName('oauth_token')]
    public ?string $oauthToken;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[SerializedName('oauth_token_secret')]
    public ?string $oauthTokenSecret;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[SerializedName('user_id')]
    public ?string $userId;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[SerializedName('screen_name')]
    public ?string $screenName;
}