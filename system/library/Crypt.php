<?php

namespace System\library;

class Crypt
{
    /**
     * Encrypts a string using a key.
     *
     * @param string $string The string to encrypt.
     * @param string $key The encryption key.
     * @return string The encrypted string.
     */
    public static function encrypt($string, $key)
    {
        if (!function_exists('openssl_encrypt')) {
            throw new \Exception('OpenSSL extension is not available.');
        }
        return openssl_encrypt($string, 'AES-256-CBC', $key, 0, substr($key, 0, 16));
    }

    /**
     * Decrypts a string using a key.
     *
     * @param string $string The string to decrypt.
     * @param string $key The decryption key.
     * @return string The decrypted string.
     */
    public static function decrypt($string, $key)
    {
        if (!function_exists('openssl_encrypt')) {
            throw new \Exception('OpenSSL extension is not available.');
        }

        return openssl_decrypt($string, 'AES-256-CBC', $key, 0, substr($key, 0, 16));
    }

    /**
     * Generates a random key for encryption.
     *
     * @return string The generated key.
     */
    public static function generateKey()
    {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new \Exception('OpenSSL extension is not available.');
        }

        return bin2hex(openssl_random_pseudo_bytes(32)); // 32 bytes for AES-256
    }
}
