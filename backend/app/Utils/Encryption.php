<?php
namespace App\Utils;

class Encryption
{
    private static string $cipher = "aes-256-cbc";
    
    // In a real app, this should be in .env (ENCRYPTION_KEY)
    // Using a fixed key for this environment if not set, to ensure data persistence works across sessions
    private static string $defaultKey = "32_byte_secure_key_for_unilink_dev!!"; 

    public static function encrypt(string $data): string
    {
        $key = self::getKey();
        $ivlen = openssl_cipher_iv_length(self::$cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($data, self::$cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        return base64_encode( $iv.$hmac.$ciphertext_raw );
    }

    public static function decrypt(string $data): ?string
    {
        $c = base64_decode($data);
        $key = self::getKey();
        $ivlen = openssl_cipher_iv_length(self::$cipher);
        
        if (strlen($c) < $ivlen + 32) {
            return null; // Invalid data length
        }

        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, 32);
        $ciphertext_raw = substr($c, $ivlen + 32);
        
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        
        if (!hash_equals($hmac, $calcmac)) {
            return null; // Timing attack safe comparison
        }

        return openssl_decrypt($ciphertext_raw, self::$cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    }

    private static function getKey(): string
    {
        // Use environment variable if available, otherwise default
        $envKey = $_ENV['ENCRYPTION_KEY'] ?? null;
        if ($envKey && strlen($envKey) === 32) {
            return $envKey;
        }
        
        // Pad or truncate default key to 32 bytes
        return substr(str_pad(self::$defaultKey, 32, '0'), 0, 32);
    }
}
