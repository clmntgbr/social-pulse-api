<?php

namespace App\Service;

readonly class OpenSslEncryption
{
    const SSL_OPTIONS = 0;
    CONST CIPHER_METHOD = 'AES-128-CTR';

    public function __construct(
        private string $encryptionKey,
        private string $initializationVector = '1234567891011121'
    ) {}

    public function encrypt(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        return openssl_encrypt(
            $value,
            self::CIPHER_METHOD,
            $this->encryptionKey,
            self::SSL_OPTIONS,
            $this->initializationVector
        );
    }

    public function decrypt(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        return openssl_decrypt(
            $value,
            self::CIPHER_METHOD,
            $this->encryptionKey,
            self::SSL_OPTIONS,
            $this->initializationVector
        );
    }
}