<?php

namespace App\Enum;

enum SocialAccountType: string
{
    case FACEBOOK = "facebook_social_account";
    case TWITTER = "twitter_social_account";
    case LINKEDIN = "linkedin_social_account";

    public function toString(): string
    {
        return $this->value;
    }
}
