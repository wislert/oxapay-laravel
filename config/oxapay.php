<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Keys
    |--------------------------------------------------------------------------
    |
    | You can define multiple API keys per type (merchant, payout, general).
    | The "default" slot will be used automatically if no key is provided
    | when calling the SDK methods.
    |
    */
    'merchants' => [
        'default' => env('OXAPAY_MERCHANT_KEY'),
        'key_2' => env('OXAPAY_MERCHANT_KEY_2'),
    ],

    'payouts' => [
        'default' => env('OXAPAY_PAYOUT_KEY'),
        'key_2' => env('OXAPAY_PAYOUT_KEY_2'),
    ],

    'general' => [
        'default' => env('OXAPAY_GENERAL_KEY'),
        'key_2' => env('OXAPAY_GENERAL_KEY_2'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Callback URLs
    |--------------------------------------------------------------------------
    |
    | Define custom callback URLs for different operations. If not provided,
    | OxaPay will use the callback URL configured on your merchant account.
    |
    */
    'callback_url' => [
        // Payments (invoice, white_label, static_address, payment_link, donation)
        'payment' => env('OXAPAY_PAYMENT_CALLBACK_URL', ''),

        // Payouts
        'payout' => env('OXAPAY_PAYOUT_CALLBACK_URL', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sandbox Mode (only for invoice creation)
    |--------------------------------------------------------------------------
    |
    | When enabled, invoice (payment) requests will be sent to the OxaPay
    | sandbox environment. Other endpoints are always processed on
    | the production environment.
    |
    */
    'sandbox' => (bool)env('OXAPAY_SANDBOX', false),


    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The maximum number of seconds to wait for each HTTP request before
    | failing with a timeout exception.
    |
    */
    'timeout' => env('OXAPAY_TIMEOUT', 20),
];
