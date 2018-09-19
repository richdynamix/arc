<?php

namespace Richdynamix\Arc;

use Illuminate\Support\ServiceProvider;
use Richdynamix\Arc\Console\InstallArc;

class ArcServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../tools' => base_path('tools'),
        ]);

        $this->publishes([
            __DIR__ . '/../stubs' => base_path('.'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            InstallArc::class,
        ]);
    }
}