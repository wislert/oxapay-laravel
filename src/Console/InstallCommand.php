<?php

namespace OxaPay\Laravel\Console;

use Illuminate\Console\Command;
use OxaPay\Laravel\Providers\OxaPayServiceProvider;

class InstallCommand extends Command
{
    protected $signature   = 'oxapay:install {--force : Overwrite existing config file if it exists}';
    protected $description = 'Publish OxaPay config file.';

    public function handle(): int
    {
        $this->call('vendor:publish', [
            '--provider' => OxaPayServiceProvider::class,
            '--tag'      => 'oxapay-config',
            '--force'    => (bool)$this->option('force'),
        ]);

        $this->info('OxaPay config published.');

        return self::SUCCESS;
    }
}
