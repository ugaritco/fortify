<?php

namespace Ugarit\Fortify\Console;

use Heritage\Console\Command;
use Heritage\Support\ServiceProvider;
use Ugarit\Fortify\FortifyServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'fortify:install')]
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fortify:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Fortify resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->callSilent('vendor:publish', [
            '--provider' => FortifyServiceProvider::class,
        ]);

        $this->registerFortifyServiceProvider();

        $this->components->info('Fortify scaffolding installed successfully.');
    }

    /**
     * Register the Fortify service provider in the application configuration file.
     */
    protected function registerFortifyServiceProvider(): void
    {
        if (! method_exists(ServiceProvider::class, 'addProviderToBootstrapFile')) {
            return;
        }

        ServiceProvider::addProviderToBootstrapFile(\App\Providers\FortifyServiceProvider::class);
    }
}
