<?php

namespace App\Service;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\MakerBundle\Str;

readonly class ImageService
{
    public function __construct(
        private string $backUrl = '',
    ) {
    }

    public function downloadTmp(string $url): ?string
    {
        if (!is_dir(sprintf('tmp'))) {
            mkdir(sprintf('tmp'), 0755, true);
        }

        // Set a timeout to prevent hanging
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
            ],
        ]);

        // Attempt to download the image
        $imageContent = file_get_contents($url, false, $context);

        if (false === $imageContent) {
            return null;
        }

        $path = sprintf('%s.png', Uuid::uuid4()->toString());

        if (false === file_put_contents(sprintf('tmp/%s', $path), $imageContent)) {
            return null;
        }

        return sprintf('tmp/%s', $path);
    }

    public function delete(string $filePath): bool 
    {
        if (!file_exists($filePath)) {
            return false;
        }

        if (!is_writable($filePath)) {
            return false;
        }

        if (unlink($filePath)) {
            return true;
        }

        return false;
    }

    public function download(string $url, string $path, string $type): ?string
    {
        if (!is_dir(sprintf('public/%s/%s', $type, $path))) {
            mkdir(sprintf('public/%s/%s', $type, $path), 0755, true);
        }

        // Set a timeout to prevent hanging
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
            ],
        ]);

        // Attempt to download the image
        $imageContent = file_get_contents($url, false, $context);

        if (false === $imageContent) {
            return null;
        }

        $path = sprintf('%s/%s.png', $path, Uuid::uuid4()->toString());

        if (false === file_put_contents(sprintf('public/%s/%s', $type, $path), $imageContent)) {
            return null;
        }

        return sprintf('%s/%s/%s', $this->backUrl, $type, $path);
    }

    public function convertToBase64(string $path): ?string
    {
        $imageData = file_get_contents($path);

        if ($imageData === false) {
            return null;
        }
        
        $base64 = base64_encode($imageData);
        return "data:image/png;base64,{$base64}";
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