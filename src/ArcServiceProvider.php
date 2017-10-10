<?php

namespace Richdynamix\Arc;

use Illuminate\Support\ServiceProvider;

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
            __DIR__.'/../docker' => base_path('.'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}