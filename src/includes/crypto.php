<?php

class Crypto {

    // Store this in ENV/config in production
    private static $key = "CHANGE_THIS_TO_A_LONG_RANDOM_SECRET_KEY_32CHARS";

    public static function encrypt($plaintext) {

        $cipher = "AES-256-CBC";

        // Generate random IV
        $iv = random_bytes(openssl_cipher_iv_length($cipher));

        $encrypted = openssl_encrypt(
            $plaintext,
            $cipher,
            self::$key,
            OPENSSL_RAW_DATA,
            $iv
        );

        // Store IV + encrypted data together
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($encryptedText) {

        $cipher = "AES-256-CBC";

        $data = base64_decode($encryptedText);

        $ivLength = openssl_cipher_iv_length($cipher);

        $iv = substr($data, 0, $ivLength);

        $encrypted = substr($data, $ivLength);

        return openssl_decrypt(
            $encrypted,
            $cipher,
            self::$key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
}