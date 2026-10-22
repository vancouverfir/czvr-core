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

    'discord' => [
        'token' => env('DISCORD_BOT_TOKEN'),
        'client_id' => env('DISCORD_KEY'),
        'client_secret' => env('DISCORD_SECRET'),
        'redirect' => env('DISCORD_REDIRECT_URI'),
        'redirect_join' => env('DISCORD_REDIRECT_URI_JOIN'),
    ],

    'vatcan' => [
        'key' => env('VATCAN_API_KEY'),
    ],

    'checkwx' => [
        'key' => env('AIRPORT_API_KEY'),
    ],

    'booking' => [
        'key' => env('BOOKING_API_KEY'),
    ],

];
