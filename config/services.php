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

    'openlibrary' => [
        'url' => env('OPEN_LIBRARY_BASE_URL', 'https://openlibrary.org'),
        'enabled' => env('OPENLIBRARY_ENABLED', true),
        'cache_duration' => env('OPENLIBRARY_CACHE_DURATION', 3600),
    ],

    'zenquotes' => [
        'url' => env('ZEN_QUOTES_BASE_URL', 'https://zenquotes.io'),
        'enabled' => env('ZENQUOTES_ENABLED', true),
        'cache_duration' => env('ZENQUOTES_CACHE_DURATION', 86400),
    ],

    'skills_api' => [
        'key' => env('SKILL_METADATA_API_KEY'),
        'url' => env('SKILLS_API_BASE_URL', 'https://emsiservices.com/skills'),
        'enabled' => env('SKILLS_API_ENABLED', true),
        'cache_duration' => env('SKILLS_API_CACHE_DURATION', 86400),
    ],

];
