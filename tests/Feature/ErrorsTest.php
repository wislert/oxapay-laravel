<?php

use Illuminate\Support\Facades\Http;
use OxaPay\Laravel\Support\Facades\OxaPay;
use OxaPay\Laravel\Exceptions\ValidationRequestException;

it('maps 400 to ValidationRequestException with correct message and context', function () {
    Http::fake([
        '*' => Http::response([
            'data'    => [],
            'message' => 'There was an issue with the submitted data. Please verify your input and try again.',
            'error'   => [
                'type'    => 'invalid_param',
                'key'     => 'lifetime',
                'message' => 'The lifetime field must be an integer.',
            ],
            'status'  => 400,
            'version' => '1.0.0',
        ], 400),
    ]);

    try {
        OxaPay::payment()->generateInvoice([
            'amount'   => 1.23,
            'currency' => 'USDT',
            'lifetime' => 1.23,
        ]);

        expect()->fail('Exception was not thrown.');

    } catch (ValidationRequestException $e) {
        $ctx = method_exists($e, 'getContext') ? $e->getContext() : [];
        expect(data_get($ctx, 'response.error.key'))->toBe('lifetime');
        expect(data_get($ctx, 'response.error.message'))->toBe('The lifetime field must be an integer.');
        expect(data_get($ctx, 'response.status'))->toBe(400);
    }
});
