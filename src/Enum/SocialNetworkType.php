<?php

namespace App\Enum;

enum SocialNetworkType: string
{
    case FACEBOOK = 'facebook';
    case TWITTER = 'twitter';
    case LINKEDIN = 'linkedin';
    case YOUTUBE = 'youtube';
    case INSTAGRAM = 'instagram';

    public function toString(): string
    {
        return $this->value;
    }
}
