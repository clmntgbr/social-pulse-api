<?php

namespace App\Message;

final class PublishScheduledPublicationsMessage
{
    private string $uuid;
    private string $socialNetworkType;

    public function __construct(string $uuid, string $socialNetworkType)
    {
        $this->uuid = $uuid;
        $this->socialNetworkType = $socialNetworkType;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getSocialNetworkType(): string
    {
        return $this->socialNetworkType;
    }
}