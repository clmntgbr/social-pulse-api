<?php

namespace App\Enum;

enum PublicationThreadType: string
{
    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';

    public function toString(): string
    {
        return $this->value;
    }
}
