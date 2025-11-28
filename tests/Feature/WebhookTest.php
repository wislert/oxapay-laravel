// tests/Feature/WebhookFacadeTest.php
<?php

use Illuminate\Http\Request;
use OxaPay\Laravel\Support\Facades\OxaPay;
use OxaPay\Laravel\Exceptions\WebhookSignatureException;

function bindRequest(string $json, ?string $hmac = null, bool $lowerHeader = false): void
{
    $server = ['CONTENT_TYPE' => 'application/json'];
    if ($hmac !== null) {
        $server[$lowerHeader ? 'HTTP_hmac' : 'HTTP_HMAC'] = $hmac;
    }
    $req = Request::create('/webhook', 'POST', [], [], [], $server, $json);
    if ($hmac !== null) {
        $req->headers->set($lowerHeader ? 'hmac' : 'HMAC', $hmac);
    }
    app()->instance('request', $req);
}

beforeEach(function () {
    config([
        'oxapay.merchants.default' => 'merchant-secret-123',
        'oxapay.payouts.default'   => 'payout-secret-456',
    ]);
});

it('verifies INVOICE webhook using config secret', function () {
    $payload = json_encode([
        'type' => 'invoice', 'status' => 'Paid', 'track_id' => '151811887',
    ], JSON_UNESCAPED_SLASHES);

    $sig = hash_hmac('sha512', $payload, 'merchant-secret-123');
    bindRequest($payload, $sig);

    $data = OxaPay::webhook()->getData(true);
    expect($data['type'])->toBe('invoice');
});

it('verifies PAYOUT webhook using config secret', function () {
    $payload = json_encode([
        'type' => 'payout', 'status' => 'Confirmed', 'track_id' => '227296189',
    ], JSON_UNESCAPED_SLASHES);

    $sig = hash_hmac('sha512', $payload, 'payout-secret-456');
    bindRequest($payload, $sig);

    $data = OxaPay::webhook()->getData(true);
    expect($data['type'])->toBe('payout');
});

it('verifies INVOICE webhook when key is passed explicitly', function () {
    $payload  = '{"type":"invoice","status":"Paid"}';
    $override = 'custom-merchant-secret';
    $sig      = hash_hmac('sha512', $payload, $override);
    bindRequest($payload, $sig);

    $data = OxaPay::webhook(merchantApiKey: $override)->getData(true);
    expect($data['status'])->toBe('Paid');
});

it('verifies PAYOUT webhook when key is passed explicitly', function () {
    $payload  = '{"type":"payout","status":"Confirmed"}';
    $override = 'custom-payout-secret';
    $sig      = hash_hmac('sha512', $payload, $override);
    bindRequest($payload, $sig);

    $data = OxaPay::webhook(payoutApiKey: $override)->getData(true);
    expect($data['status'])->toBe('Confirmed');
});

it('accepts lowercase hmac header', function () {
    $payload = '{"type":"invoice","status":"Paid"}';
    $sig     = hash_hmac('sha512', $payload, 'merchant-secret-123');
    bindRequest($payload, $sig, lowerHeader: true);

    $data = OxaPay::webhook()->getData(true);
    expect($data['status'])->toBe('Paid');
});

it('throws when HMAC header is missing', function () {
    bindRequest('{"type":"invoice","status":"Paid"}', null);
    OxaPay::webhook()->getData(true);
})->throws(WebhookSignatureException::class, 'Missing HMAC header.');

it('throws on invalid signature', function () {
    bindRequest('{"type":"invoice","status":"Paid"}', 'deadbeef');
    OxaPay::webhook()->getData(true);
})->throws(WebhookSignatureException::class, 'Invalid HMAC signature.');
