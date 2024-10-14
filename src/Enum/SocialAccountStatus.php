<?php

namespace App\Enum;

enum SocialAccountStatus: string
{
    case ACTIF = "is_actif";
    case EXPIRED = "is_expired";
    case EXPIRE_SOON = "expire_soon";
    case TEMPORARY = "temporary";

    public function toString(): string
    {
        return $this->value;
    }
}
