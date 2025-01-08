<?php

namespace App\Enum;

enum SocialNetworkStatus: string
{
    case ACTIVE = 'active';
    case TEMPORARY = 'temporary';

    public function toString(): string
    {
        return $this->value;
    }
}
