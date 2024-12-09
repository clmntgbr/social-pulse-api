<?php

namespace App\Service;

use Ramsey\Uuid\Uuid;

readonly class ImageService
{
    public function __construct(
        private string $backUrl = ''
    ) {}

    public function download(string $url, string $path, string $type): ?string
    {
        if (!is_dir(sprintf('public/%s/%s', $type, $path))) {
            mkdir(sprintf('public/%s/%s', $type, $path), 0755, true);
        }

        // Set a timeout to prevent hanging
        $context = stream_context_create([
            'http' => [
                'timeout' => 30
            ]
        ]);

        // Attempt to download the image
        $imageContent = file_get_contents($url, false, $context);

        if ($imageContent === false) {
            return null;
        }

        $path = sprintf('%s/%s.png', $path, Uuid::uuid4()->toString());

        if (file_put_contents(sprintf('public/%s/%s', $type, $path), $imageContent) === false) {
            return null;
        }

        return sprintf('%s/%s/%s', $this->backUrl, $type, $path);
    }

    public function saveBase64File(string $type, string $uuid, ?string $base64String): ?string
    {
        if (!is_dir(sprintf('%s/%s', $type, $uuid))) {
            mkdir(sprintf('%s/%s', $type, $uuid), 0755, true);
        }

        if (is_null($base64String)) {
            return null;
        }

        $base64String = explode(',', $base64String)[1];
        $imageData = base64_decode($base64String);

        $filePath = sprintf('%s/%s/%s.png', $type, $uuid, Uuid::uuid4()->toString());
        file_put_contents($filePath, $imageData);

        return sprintf('%s/%s', $this->backUrl, $filePath);
    }
}