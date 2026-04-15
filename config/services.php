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

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'dynamic_services' => [
        'message' => \App\Services\SmsService::class,
    ],

    // 'firebase' => [
    //     'credentials' => storage_path(env('FIREBASE_CREDENTIALS')),
    //     'project_id'  => env('FIREBASE_PROJECT_ID'),
    // ],
    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => env('FACEBOOK_REDIRECT_URI'),
    ],
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    'opensearch' => [
        'hosts' => explode(',', env('OPENSEARCH_HOSTS', 'http://localhost:9200')),
        'basic_auth' => [
            'username' => env('OPENSEARCH_USERNAME', 'admin'),
            'password' => env('OPENSEARCH_PASSWORD', 'admin'),
        ],
        'ssl_verification' => env('OPENSEARCH_SSL_VERIFICATION', false),
    ],
];
