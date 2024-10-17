<?php

namespace App\Message;

final class DeleteTemporarySocialAccount
{
    private string $uuid;

    public function __construct(string $uuid) {
        $this->uuid = $uuid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
