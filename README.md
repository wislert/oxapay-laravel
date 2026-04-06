# OxaPay Laravel SDK

Official Laravel SDK for [OxaPay](https://oxapay.com) — accept crypto payments, exchanges, and payouts.

> **Frameworks:** Laravel 8–13  
> **PHP:** 8.0+ (Laravel 8–9) / 8.1+ (Laravel 10) / 8.2+ (Laravel 11–12) / 8.3+ (Laravel 13)   
> **Docs:** https://docs.oxapay.com

## Installation

```bash
composer require oxapay/oxapay-laravel
```

### Publish `config/oxapay.php`
```bash
php artisan oxapay:install
```
> You can use `--force` flag to overwrite config from package

> Service provider and facade are auto-discovered.


### Add your keys to `.env` or update oxapay config:

```dotenv
OXAPAY_MERCHANT_KEY=your_merchant_api_key
OXAPAY_PAYOUT_KEY=your_payout_api_key
OXAPAY_GENERAL_KEY=your_general_api_key
```
> You can define multiple slots.

---
## Quick start
```php
use OxaPay\Laravel\Support\Facades\OxaPay;

// via facade
$res = OxaPay::payment()->generateInvoice([
    'amount' => 10.5,
    'currency' => 'USDT'
]);

// via helper
$res = oxapay()->payment()->generateInvoice([ 
    'amount' => 10.5,
    'currency' => 'USDT'
]);

// key is optional and use default key from config if no passed
$res = OxaPay::payment('key_2')->generateInvoice([
    'amount' => 10.5,
    'currency' => 'USDT'
]);

// or use raw key
$res = OxaPay::payment("XXXXXX-XXXXXX-XXXXXX-XXXXXX")->generateInvoice([
    'amount' => 10.5,
    'currency' => 'USDT'
]);

```



## Handling Webhooks (Payments & Payouts)
```php
use OxaPay\Laravel\Support\Facades\OxaPay;

try{
    $res = OxaPay::webhook()->getData();
    // ...
}catch (WebhookSignatureException $e) {
    // ...
}


// or you can get data without verify HMAC
$res = OxaPay::webhook()->getData(false);

```


---
## Available methods
### 🔹payment
- `generateInvoice` – Create invoice & get payment URL. [More details](https://docs.oxapay.com/api-reference/payment/generate-invoice)
- `generateWhiteLabel` – White-label payment. [More details](https://docs.oxapay.com/api-reference/payment/generate-white-label)
- `generateStaticAddress` – Create static deposit address. [More details](https://docs.oxapay.com/api-reference/payment/generate-static-address)
- `revokeStaticAddress` – Revoke static address. [More details](https://docs.oxapay.com/api-reference/payment/revoking-static-address)
- `staticAddressList` – List static addresses. [More details](https://docs.oxapay.com/api-reference/payment/static-address-list)
- `information` – Single payment information. [More details](https://docs.oxapay.com/api-reference/payment/payment-information)
- `history` – Payment history list. [More details](https://docs.oxapay.com/api-reference/payment/payment-history)
- `acceptedCurrencies` – Accepted currencies. [More details](https://docs.oxapay.com/api-reference/payment/accepted-currencies)

### 🔹account
- `balance` – Account balance. [More details](https://docs.oxapay.com/api-reference/common/account-balance)

### 🔹payout
- `generate` – Request payout. [More details](https://docs.oxapay.com/api-reference/payout/generate-payout)
- `information` – Single payout information. [More details](https://docs.oxapay.com/api-reference/payout/payout-information)
- `history` – Payout history list. [More details](https://docs.oxapay.com/api-reference/payout/payout-history)

### 🔹exchange
- `swapRequest` – Swap request. [More details](https://docs.oxapay.com/api-reference/swap/swap-request)
- `swapHistory` – Swap history. [More details](https://docs.oxapay.com/api-reference/swap/swap-history)
- `swapPairs` – Swap pairs. [More details](https://docs.oxapay.com/api-reference/swap/swap-pairs)
- `swapCalculate` – Swap pre-calc. [More details](https://docs.oxapay.com/api-reference/swap/swap-calculate)
- `swapRate` – Swap Quote rate. [More details](https://docs.oxapay.com/api-reference/swap/swap-rate)

### 🔹common
- `prices` – Market prices. [More details](https://docs.oxapay.com/api-reference/common/prices)
- `currencies` – Supported crypto. [More details](https://docs.oxapay.com/api-reference/common/supported-currencies)
- `fiats` – Supported fiats. [More details](https://docs.oxapay.com/api-reference/common/supported-fiat-currencies)
- `networks` – Supported networks. [More details](https://docs.oxapay.com/api-reference/common/supported-networks)
- `monitor` – System status. [More details](https://docs.oxapay.com/api-reference/common/system-status)

### 🔹webhook
- `verify` – Validates `HMAC` header (sha512 of raw body).
- `getData` – Validates `HMAC` header and return webhook data. [More details](https://docs.oxapay.com/webhook)


---
## Exceptions
All SDK exceptions extend `OxaPay\Laravel\Exceptions\OxaPayException`:
- `ValidationRequestException` (HTTP 400)
- `InvalidApiKeyException` (HTTP 401)
- `NotFoundException` (HTTP 404)
- `RateLimitException` (HTTP 429)
- `ServerErrorException` (HTTP 500)
- `ServiceUnavailableException` (HTTP 503)
- `HttpException` (network/unknown)
- `MissingApiKeyException` (missing api key)
- `MissingTrackIdException` (missing track id)
- `MissingAddressException` (missing address)
- `WebhookSignatureException` (bad/missing HMAC)
- `WebhookNotReceivedException` (webhook request was not received)



### Security Notes
- Verify webhook HMAC before use input data.
- Whitelist OxaPay IPs on your firewall (ask support).
- Use HTTPS everywhere.
- Store keys in `.env`, not code.
- Rotate keys regularly.
---


## Testing (safe & offline)

This package uses **Pest**, **PHPUnit**, and **Orchestra Testbench** for testing.  
Dependencies are already listed under `require-dev` in `composer.json`.

Run tests with composer:

```bash
composer test
```

Run tests with pest:

```bash
vendor/bin/pest
```

---
## Compatibility

- Laravel 8–9 → PHP 8.0+
- Laravel 10 → PHP 8.1+
- Laravel 11–12 → PHP 8.2+


## Security

If you discover a security vulnerability, please email [contact@oxapay.com](mailto:contact@oxapay.com).  
Do not disclose publicly until it has been fixed.

## Contributing

Pull requests are welcome. For major changes, open an issue first.  
Run coding standards & static analysis before PR:

```bash
composer cs-fix
composer phpstan
composer test
```


## License

Apache-2.0 — see [LICENSE](https://github.com/OxaPay/oxapay-laravel/blob/HEAD/LICENSE).

## Changelog

See [CHANGELOG.md](https://github.com/OxaPay/oxapay-laravel/blob/HEAD/CHANGELOG.md) for version history.


---
OxaPay Made with ♥ for Laravel.
