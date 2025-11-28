<?php

namespace OxaPay\Laravel\Providers;

use OxaPay\Laravel\OxaPayManager;
use Illuminate\Support\ServiceProvider;
use OxaPay\Laravel\Console\InstallCommand;

class OxaPayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // update config
        $this->mergeConfigFrom(__DIR__ . '/../../config/oxapay.php', 'oxapay');

        // register OxaPayManager
        $this->app->singleton('oxapay.manager', function () {
            return new OxaPayManager();
        });
    }

    public function boot(): void
    {
        // Register command
        if ($this->app->runningInConsole()) {
            // Set publish for config file
            $this->publishes([__DIR__ . '/../../config/oxapay.php' => config_path('oxapay.php')], 'oxapay-config');

            // Set command
            $this->commands([InstallCommand::class]);
        }
    }
}
