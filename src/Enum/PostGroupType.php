<?php

namespace App\Enum;

enum PostGroupType: string
{
    case MASTER = "master";
    case SLAVE = "slave";

    public function toString(): string
    {
        return $this->value;
    }
}
