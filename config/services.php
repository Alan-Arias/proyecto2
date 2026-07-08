<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'pagofacil' => [
        'modo' => env('PAGOFACIL_MODO', 'simulado'),
        'url_base' => env('PAGOFACIL_URL_BASE', 'https://masterqr.pagofacil.com.bo/api/services/v2'),
        'token_service' => env('PAGOFACIL_TOKEN_SERVICE', env('PAGOFACIL_TOKEN')),
        'token_secret' => env('PAGOFACIL_TOKEN_SECRET'),
        'commerce_id' => env('PAGOFACIL_COMMERCE_ID', env('PAGOFACIL_COMERCIO_ID')),
        'payment_method_id' => env('PAGOFACIL_PAYMENT_METHOD_ID'),
        'currency' => (int) env('PAGOFACIL_CURRENCY', 2),
        'callback_url' => env('PAGOFACIL_CALLBACK_URL'),
        'checkout_url' => env('PAGOFACIL_CHECKOUT_URL', 'https://checkout.pagofacil.com.bo/es/pay'),
        'response_language' => env('PAGOFACIL_RESPONSE_LANGUAGE', 'es'),
        'default_email' => env('PAGOFACIL_DEFAULT_EMAIL', 'pagos@autoescuelaamerica.com.bo'),
        'default_phone' => env('PAGOFACIL_DEFAULT_PHONE', '77777777'),
        'timeout' => (int) env('PAGOFACIL_TIMEOUT', 20),
    ],

];
