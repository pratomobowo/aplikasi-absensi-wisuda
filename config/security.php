<?php

return [

    /*
    |--------------------------------------------------------------------------
    | QR Code Encryption Settings
    |--------------------------------------------------------------------------
    |
    | These settings control the encryption of QR code data. The encryption
    | key should be a base64-encoded 32-byte random string. You can generate
    | one using: openssl rand -base64 32
    |
    */

    'qr_encryption' => [
        'key' => env('QR_ENCRYPTION_KEY'),
        'cipher' => env('QR_ENCRYPTION_CIPHER', 'AES-256-CBC'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | These headers are automatically added to all responses to enhance
    | security. They help prevent XSS, clickjacking, and other attacks.
    |
    */

    'headers' => [
        'x_content_type_options' => 'nosniff',
        'x_frame_options' => 'SAMEORIGIN',
        'x_xss_protection' => '1; mode=block',
        'referrer_policy' => 'strict-origin-when-cross-origin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for different parts of the application.
    | Values are in requests per minute.
    |
    */

    'rate_limits' => [
        'invitation_access' => 10,  // Per IP
        'scanner_api' => 30,        // Per user
        'pdf_download' => 5,        // Per token
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Expiration
    |--------------------------------------------------------------------------
    |
    | Configure how long various tokens remain valid (in days).
    |
    */

    'token_expiration' => [
        'magic_link' => 30,         // Days until magic link expires
        'qr_code' => 30,            // Days until QR code expires
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Validation
    |--------------------------------------------------------------------------
    |
    | Configure validation patterns for user inputs.
    |
    */

    'validation' => [
        'nim_pattern' => '/^[A-Z0-9]+$/',
        'name_pattern' => '/^[a-zA-Z\s.]+$/',
        'phone_pattern' => '/^[0-9+\-\s()]+$/',
    ],

];
