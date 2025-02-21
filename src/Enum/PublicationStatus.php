<?php

namespace App\Enum;

enum PublicationStatus: string
{
    case POSTED = 'posted';
    case SCHEDULED = 'scheduled';
    case FAILED = 'failed';
    case RETRY = 'retry';
    case DRAFT = 'draft';

    public function toString(): string
    {
        return $this->value;
    }
}
