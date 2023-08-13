<?php

return [
    'ENV' => env('DHL_ENV', 'TEST'),
    'TEST' => [
        'baseUrl' => env('DHL_TEST_URL'),
        'accountNumber' => env('DHL_ACCOUNT_NUMBER'),
        'importAccountNumber' => env('DHL_IMPORT_ACCOUNT_NUMBER'),
        'username' => env('DHL_TEST_USERNAME'),
        'password' => env('DHL_TEST_PASSWORD'),
    ],

    'LIVE' => [
        'baseUrl' => env('DHL_LIVE_URL'),
        'accountNumber' => env('DHL_ACCOUNT_NUMBER'),
        'importAccountNumber' => env('DHL_IMPORT_ACCOUNT_NUMBER'),
        'username' => env('DHL_LIVE_USERNAME'),
        'password' => env('DHL_LIVE_PASSWORD'),
    ]
];
