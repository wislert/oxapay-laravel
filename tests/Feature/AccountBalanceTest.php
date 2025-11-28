<?php

namespace OxaPay\Laravel\Tests\Feature;

use Illuminate\Support\Facades\Http;
use OxaPay\Laravel\Support\Facades\OxaPay;

it('returns account balance (no currency filter) with correct shape', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'LTC'  => 0,
                'USDC' => 116.850922859,
                'XMR'  => 0.0001,
                'BTC'  => 0.0022866845,
                'ETH'  => 0.2129870236,
                'POL'  => 90.9419370614,
                'SOL'  => 0.003372,
                'NOT'  => 0,
                'SHIB' => 21715327.987896618,
                'XRP'  => 0,
                'TRX'  => 8617.7293704753,
                'USDT' => 9779.0227002478,
                'DOGS' => 3237160.709744972,
                'TON'  => 0,
                'BCH'  => 0,
                'DOGE' => 0,
                'BNB'  => 0.007404826,
            ],
            'message' => 'Operation completed successfully!',
            'error'   => (object)[],
            'status'  => 200,
            'version' => '1.0.0',
        ], 200),
    ]);

    $res = OxaPay::account()->balance();

    expect($res)->toBeArray()
        ->and($res)->toHaveKey('USDT')
        ->and($res['USDT'])->toBeNumeric();
});

it('returns only requested currency when currency filter is provided', function () {
    Http::fake(['*' => Http::response([
        'data'    => ['BNB' => 0.007404826],
        'message' => 'Operation completed successfully!','error' => (object)[],
        'status'  => 200,'version' => '1.0.0',
    ], 200)]);

    $res = OxaPay::account()->balance('BNB');

    expect($res)->toBeArray()
        ->and($res)->toHaveCount(1)
        ->and($res)->toHaveKey('BNB')
        ->and($res['BNB'])->toEqual(0.007404826);
});
