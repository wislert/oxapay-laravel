<?php

namespace OxaPay\Laravel\Tests\Feature;

use Illuminate\Support\Facades\Http;
use OxaPay\Laravel\Support\Facades\OxaPay;

it('create invoice and returns track_id, payment_url, expired_at, date', function () {
    Http::fake(['*' => Http::response([
        'data' => [
            'track_id'    => '193139644',
            'payment_url' => 'https://pay.oxapay.com/13355044/193139644',
            'expired_at'  => 1755999478,
            'date'        => 1755997678,
        ],
        'message' => 'Operation completed successfully!',
        'error'   => (object)[],
        'status'  => 200,
        'version' => '1.0.0',
    ], 200)]);

    $res = OxaPay::payment()->generateInvoice([
        'amount'   => 1.23,
        'currency' => 'USDT',
    ]);

    expect($res)->toBeArray()
        ->and($res)->toHaveKeys(['track_id','payment_url','expired_at','date'])
        ->and($res['track_id'])->toBeString()
        ->and($res['expired_at'])->toBeInt();
});
