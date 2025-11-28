<?php

namespace OxaPay\Laravel\Tests;

use OxaPay\Laravel\Support\Facades\OxaPay;
use Orchestra\Testbench\TestCase as BaseTestCase;
use OxaPay\Laravel\Providers\OxaPayServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [OxaPayServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return ['OxaPay' => OxaPay::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));

        $app['config']->set('oxapay', require __DIR__ . '/../config/oxapay.php');

        $app['config']->set('oxapay.general.default', 'test_key');
        $app['config']->set('oxapay.merchants.default', 'test_key');
        $app['config']->set('oxapay.payouts.default', 'test_key');
    }
}
